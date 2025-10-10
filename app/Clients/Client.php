<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\PendingRequest;
use App\Exceptions\HttpClientException;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Log;

abstract class Client
{
    protected string $url;
    protected string $apiKey;
    protected $options = [
        'verify' => true,
    ];

    public function sendGet(string $path, array $data = []): array
    {
        $data = [...$data, 'api-key' => $this->apiKey];
        return $this->sendRequest()
            ->get("{$this->url}/$path", $data)->json();
    }

    private function sendRequest(): PendingRequest
    {
        $request = Http::withOptions($this->options)->acceptJson();

        return $request->throw(function (Response $response, RequestException $e) {
            Log::error('Http client request failed', [
                'response' => $response->json(),
            ]);
            throw new HttpClientException($e->getMessage(), $e->getCode());
        });
    }

    // Todo: Use this.
    public function sendConcurrentGetRequest(array $urls): array
    {
        return Http::pool(function (Pool $pool) use ($urls) {
            $requests = [];

            foreach ($urls as $url) {
                $request[$url] = $pool->withOptions($this->options)
                    ->acceptJson()
                    ->withToken()
                    ->throw(function (Response $response, RequestException $e) {
                        throw new HttpClientException($e->getMessage(), $e->getCode());
                    })->get($url);
            }

            return $requests;
        });
    }
}
