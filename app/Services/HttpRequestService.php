<?php
/**
 * Created by PhpStorm.
 * User: ascet
 * Date: 12.07.15
 * Time: 17:23
 */

namespace App\Services;

use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Log\Writer;
use GuzzleHttp\Client;

class HttpRequestService
{
    private $logger;

    public function __construct() {
        $this->logger = app(Writer::class);
    }

    public function sendPost($url, $data) {
        $client = new Client($url);

        $time = microtime(true);

        $this->logRequest('post', $url, $data);

        $response = $client->post($url, $data)->send();

        $this->logResponse($response, $time);

        return $response;
    }

    public function sendGet($url, $data = null) {
        $client = new Client();

        $this->logRequest('get', $url, $data);

        $time = microtime(true);

        try {
            if (!empty($data)) {
                $response = $client->get($url, ['query' => $data]);
            } else {
                $response = $client->get($url);
            }
        } catch (BadResponseException $e) {
            $this->logResponse($e->getResponse(), $time);

            throw $e;
        }

        $this->logResponse($response);

        return $response;
    }

    protected function logRequest($typeOfRequest, $url, $data) {
        if (config('app.debug')) {
            $this->logger->info('');
            $this->logger->info('-------------------------------------');
            $this->logger->info('');
            $this->logger->info("sending {$typeOfRequest} request:", [
                'url' => $url,
                'data' => $data
            ]);
            $this->logger->info('');
        }
    }

    protected function logResponse($response, $time = null) {
        if (config('app.debug')) {
            $this->logger->info('');
            $this->logger->info('-------------------------------------');
            $this->logger->info('');
            $this->logger->info('getting response: ');
            $this->logger->info('code', ["<{$response->getStatusCode()}>"]);
            $this->logger->info('body', ["<{$response->getBody(true)}>"]);
            $this->logger->info('time', [!empty($time) ? (microtime(true) - $time) : null]);
            $this->logger->info('');
        }
    }
}