<?php

require __DIR__.'/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
$filename = $argv[1];

$spreadsheet = $reader->load($filename);
$calculationService = new \App\Service\CalculationService;
$inputProcessor = new \App\Service\InputProcessor($calculationService, $spreadsheet);

$inputProcessor->process();