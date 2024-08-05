<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TomtomService
{
    private $apiKey;
    private $baseUrl;
    private $versionNumber = 2;
    private $returnExtension = 'json';
    private $countrySet = 'HU';
    private $language = 'en-GB';

    public function __construct()
    {
        $this->apiKey = env('TOMTOM_API_KEY', '');
        $this->baseUrl = env('TOMTOM_BASE_URL', '');
    }

    public function search(string $query)
    {
        $response = Http::withUrlParameters([
            'endpoint' => $this->baseUrl,
            'version' => $this->versionNumber,
            'extension' => $this->returnExtension,
            'query' => $query
        ])->get('{+endpoint}/search/{version}/search/{query}.{extension}', [
            'key' => $this->apiKey,
            'countrySet' => $this->countrySet,
            'language' => $this->language,
            'idxSet' => 'Str,PAD',
            'typeahead' => true
        ]);

        return json_decode($response->body())->results;
    }
}