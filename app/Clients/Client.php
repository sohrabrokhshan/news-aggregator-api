<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\PendingRequest;
use App\Exceptions\HttpClientException;
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
}
