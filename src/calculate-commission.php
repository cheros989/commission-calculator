<?php

require __DIR__.'/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Xls;
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
$filename = $argv[1];

$spreadsheet = $reader->load($filename);
$ruleService = new \App\Service\RuleService();
$calculationService = new \App\Service\CalculationService($ruleService);
$inputProcessor = new \App\Service\InputProcessor($calculationService, $spreadsheet);

$inputProcessor->process();