<?php
/**
 * Created by PhpStorm.
 * User: ascet
 * Date: 05.01.16
 * Time: 12:49
 */

namespace App\Services\ExternalServices;


use App\DAL\Model\Genre;
use App\Events\NeedMoreBands;
use App\Models\Band;
use App\Models\Image;
use App\Services\HttpRequestService;

class EchoNestService {
    protected $httpService;

    public function __construct() {
        $this->httpService = app(HttpRequestService::class);
    }

    public function updateArtists($genres) {
        foreach ($genres as $genre) {
            $artists = $this->uploadBandsByGenre($genre->name);

            foreach ($artists as $artist) {
                $mainGenre = $this->getMainGenre($artist['data']['genres']);

                if (empty($mainGenre)) {
                    continue;
                }

                $band = Band::updateOrCreate(['name' => $artist['name']], [
                        'name' => $artist['name'],
                        'data' => json_encode($artist['data']),
                        'assigned_genre' => $mainGenre
                    ])
                    ->get()->toArray();

                foreach ($artist['data']['images'] as $image) {
                    Image::updateOrCreate( ['link' => $image], [
                            'link' => $image,
                            'band_id' => $band[0]['id'],
                            'status' => 'new'
                        ]);
                }
            }
        }
    }

    protected function getMainGenre($genres) {
        return array_first($genres, function ($i, $genre) {
            return Genre::where('name', $genre)->exists();
        });
    }

    public function getAllGenres() {
        $genres = [];
        $total = 1;

        while (count($genres) < $total) {
            $responseJson = $this->httpService->sendGet(
                config('services.echonest.url') . 'genre/list/',
                [
                    'api_key' => config('services.echonest.api_key'),
                    'start' => count($genres)
                ]
            )->getBody();

            $response = json_decode($responseJson, true);

            $genres = array_merge($genres, $response['response']['genres']);

            $total = $response['response']['total'];
        }

        return $genres;
    }

    public function getBandsByStyle($genre) {
        $bands = Band::where('assigned_genre', $genre)->lists();

        if (count($bands) < config('services.echonest.threshold')) {
            event(app(NeedMoreBands::class)->setGenre($genre));
        }

        return $bands;
    }

    public function uploadBandsByGenre($genre, $count = 100) {
        $responseJson = $this->httpService->sendGet(
            $this->getArtistByStyleUrl($genre, $count)
        )->getBody();
        $response = json_decode($responseJson, true);

        return array_map(function ($artist) {
            return [
                'name' => $artist['name'],
                'data' => [
                    'genres' => array_pluck($artist['genres'], 'name'),
                    'images' => array_pluck($artist['images'], 'url')
                ]
            ];
        }, $response['response']['artists']);
    }

    private function getArtistByStyleUrl($style, $count) {
        return config('services.echonest.url') .
            'artist/search?' .
            http_build_query([
                'api_key' => config('services.echonest.api_key'),
                'format' => 'json',
                'style' => $style,
                'results' => $count,
                'bucket' => 'images'
            ]) .
            '&bucket=genre';
    }
}