<?php

require __DIR__.'/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../', '.env');
$dotenv->load();

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
$filename = $argv[1];

$exchangeApiClient = new \App\Client\ExchangeApiClient($_ENV['EXCHANGE_REST_API_KEY']);
$response = $exchangeApiClient->getLatestCurrenciesRates($_ENV['BASE_CURRENCY'], explode(',', $_ENV['SUPPORTED_CURRENCIES']));
$rates = $response['rates'];
//$rates = ['USD' => 1.1497, 'JPY' => 129.53];
$spreadsheet = $reader->load($filename);
$calculationService = new \App\Service\CalculationService($rates, $_ENV['BASE_CURRENCY']); // Change base currency if you want
$inputProcessor = new \App\DataProcessor\CsvDataProcessor($calculationService, $spreadsheet);

$inputProcessor->process();