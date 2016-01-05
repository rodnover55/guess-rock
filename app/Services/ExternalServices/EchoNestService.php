<?php
/**
 * Created by PhpStorm.
 * User: ascet
 * Date: 05.01.16
 * Time: 12:49
 */

namespace App\Services\ExternalServices;


use App\Services\HttpRequestService;
use GuzzleHttp\Psr7\Response;

class EchoNestService {
    protected $httpService;

    public function __construct() {
        $this->httpService = app(HttpRequestService::class);
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

    public function getRandomArtists($style) {

    }

    public function getArtistsByStyle($style, $count = 100) {
        $results = [];

        $this->httpService->sendGet(
            config('services.echonest.url') . 'artist/search',
            [
                'api_key' => config('services.echonest.api_key'),
                'format' => 'json',
                'style' => $style,
                'results' => $count,
                'bucket' => 'images'
            ]
        );

        return $results;
    }
}