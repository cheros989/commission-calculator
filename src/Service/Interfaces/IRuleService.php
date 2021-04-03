<?php

namespace App\Service\Interfaces;

use App\Model\Client;
use App\Model\Transaction;

interface IRuleService
{
    public function getCommissionPercent(Client $client, Transaction $transaction): float;
}