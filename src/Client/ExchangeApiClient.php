<?php


namespace App\Client;

use GuzzleHttp\Client;

class ExchangeApiClient
{
    private string $apiKey;
    private Client $client;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->client = new Client([
            'base_uri' => 'http://api.exchangeratesapi.io/',
        ]);
    }

    public function getLatestCurrenciesRates(string $baseCurrency, array $supportedCurrencies): array
    {
        $response = \json_decode($this->client->get('latest', [
            'query' => [
                'access_key' => $this->apiKey,
                'base' => $baseCurrency,
                'symbols' => implode(',', $supportedCurrencies)
            ]
        ])->getBody()->getContents(), true);

        if (! $response['success']) {
            throw new \Exception($response['error']['info']);
        }

        return $response;
    }
}
