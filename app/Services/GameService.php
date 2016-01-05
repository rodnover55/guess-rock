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

class GameService {
    public function generateGame() {
        $genres = $this->getRandomGenres();

        $occupiedIds = [];

        $tasks = [];

        foreach ($genres as $genre) {
            $bands = $this->getBandsByStyle($genre, $occupiedIds);

            $occupiedIds = array_merge($occupiedIds);

            $tasks[] = [
                'genre' => $genre,
                'image' => array_rand($bands->image),
                'answer_choices' => [
                    $genre,
                    'some_choices'
                ]
            ];
        }
    }

    protected function getRandomGenres() {
        $count = config('rock_expert.tasks.count');

        $genres = Genre::all()->toArray();

        $indexes = array_rand($genres, $count);

        $result = [];
        foreach ($indexes as $index) {
            $result[] = $genres[$index];
        }
        return $result;
    }

    protected function getBandsByStyle($genre, $extendIds = []) {
        $bands = Band::where('assigned_genre', $genre)
            ->whereNotIn('id', $extendIds)
            ->get();

        if (count($bands) < config('services.echonest.threshold')) {
            event(app(NeedMoreBands::class)->setGenre($genre));
        }

        return $bands;
    }
}