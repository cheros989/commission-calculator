<?php

namespace App\Service\Interfaces;

use App\Model\Client;
use App\Model\Transaction;

interface ICalculationService
{
    public function calculateCommissionFee(Client $client, Transaction $transaction);
}
