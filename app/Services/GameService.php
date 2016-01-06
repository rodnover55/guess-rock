<?php
/**
 * Created by PhpStorm.
 * User: ascet
 * Date: 05.01.16
 * Time: 22:31
 */

namespace App\Services;


use App\DAL\Model\Genre;
use App\Events\NeedMoreBands;
use App\Models\Band;
use Illuminate\Support\Facades\DB;

class GameService {
    public function generateGame() {
        $genres = $this->getRandomGenres();

        $occupiedIds = [];

        $tasks = [];

        foreach ($genres as $genre) {
            $bands = $this->getBandsByGenre($genre, $occupiedIds);

            $occupiedIds = array_merge($occupiedIds, array_pluck($bands->toArray(), 'id'));

            $band = $bands[array_rand($bands->toArray())];
            $images = $band->images->toArray();

            $tasks[] = [
                'genre' => $genre,
                'band' => $band->toArray(),
                'image' => $images[array_rand($images)]['link'],
                'answer_choices' => array_merge([$genre['name']], $this->getWrongChoices($band))
            ];
        }

        return $tasks;
    }

    protected function getWrongChoices($band) {
        $bandGenres = json_decode($band->data, true)['genres'];

        return Genre::where('approved', true)
            ->whereNotIn('name', $bandGenres)
            ->orderBy(DB::raw('random()'))
            ->limit(config('rock_expert.tasks.choices') - 1)
            ->lists('name')->toArray();
    }

    protected function getRandomGenres() {
        return Genre::where('approved', true)
            ->orderBy(DB::raw('random()'))
            ->limit(config('rock_expert.tasks.count'))
            ->get()->toArray();
    }

    protected function getBandsByGenre($genre, $excludeIds = []) {
        $query = Band::where('assigned_genre', $genre['name']);

        if (!empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }

        $bands = $query->get();

        if (count($bands) < config('services.echonest.threshold')) {
            event(app(NeedMoreBands::class)->setGenre($genre));
        }

        return $bands;
    }
}