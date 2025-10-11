<?php

namespace App\Clients;

use SimpleXMLElement;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use App\Exceptions\HttpClientException;
use Illuminate\Http\Client\RequestException;

abstract class RSSFeed
{
    protected string $url;
    protected $options = [
        'verify' => true,
    ];

    public function sendRequest($path): SimpleXMLElement
    {
        $request = Http::withOptions($this->options);

        $response = $request->throw(function (Response $response, RequestException $e) {
            Log::error('Http client request failed', [
                'response' => $response->json(),
            ]);
            throw new HttpClientException($e->getMessage(), $e->getCode());
        })->get("{$this->url}/$path")->body();

        $xml = simplexml_load_string($response);

        if (!$xml) {
            throw new HttpClientException('Invalid XML format');
        }

        return $xml;
    }
}
