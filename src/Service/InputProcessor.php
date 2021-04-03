<?php

namespace App\Service;

use App\Model\Client;
use App\Model\Transaction;
use App\Service\Interfaces\ICalculationService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;


class InputProcessor
{
    const COLUMN_CLIENT_ID = 'B';
    const COLUMN_CLIENT_TYPE = 'C';
    const COLUMN_TRANSACTION_DATE = 'A';
    const COLUMN_TRANSACTION_TYPE = 'D';
    const COLUMN_TRANSACTION_AMOUNT = 'E';
    const COLUMN_TRANSACTION_CURRENCY = 'F';

    private Spreadsheet $spreadsheet;
    private array $clients;
    private ICalculationService $calculationService;

    public function __construct(ICalculationService $calculationService, Spreadsheet $spreadsheet, array $clients = [])
    {
        $this->spreadsheet = $spreadsheet;
        $this->clients = $clients;
        $this->calculationService = $calculationService;
    }

    public function process(): array
    {
        $rowIterator = $this->spreadsheet->getActiveSheet()->getRowIterator();

        foreach ($rowIterator as $row) {
            $client = $this->getClientFromRow($row);
            $transaction = $this->getTransactionFromRow($row);
            $commissionFee = $this->calculationService->calculateCommissionFee($client, $transaction);
//            var_dump($commissionFee);
            $client->addTransaction($transaction);
        }

        return $this->clients;
    }

    private function getClientFromRow(Row $record): Client
    {
        $cellIterator = $record->getCellIterator();
        $id = $cellIterator->seek(self::COLUMN_CLIENT_ID)->current()->getValue();
        $type = $cellIterator->seek(self::COLUMN_CLIENT_TYPE)->current()->getValue();

        // return client if exists
        if (isset($this->clients[$id])) {
            return $this->clients[$id];
        }

        // otherwise create a new one and return
        $newClient = new Client($id, $type);
        $this->clients[$id] = $newClient;

        return $newClient;
    }

    private function getTransactionFromRow(Row $record): Transaction
    {
        $cellIterator = $record->getCellIterator();
        $type = $cellIterator->seek(self::COLUMN_TRANSACTION_TYPE)->current()->getValue();
        $currency = $cellIterator->seek(self::COLUMN_TRANSACTION_CURRENCY)->current()->getValue();
        $amount = $cellIterator->seek(self::COLUMN_TRANSACTION_AMOUNT)->current()->getValue();
        $date = new \DateTime($cellIterator->seek(self::COLUMN_TRANSACTION_DATE)->current()->getValue());

        return new Transaction($type, $currency, $amount, $date);
    }
}