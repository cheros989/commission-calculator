<?php

namespace App\Service;


use App\Model\Client;
use App\Model\Transaction;
use App\Service\Interfaces\ICalculationService;
use App\Service\Interfaces\IRuleService;


class CalculationService implements ICalculationService
{
    private IRuleService $ruleService;

    public function __construct(IRuleService $ruleService)
    {
        $this->ruleService = $ruleService;
    }

    public function calculateCommissionFee(Client $client, Transaction $transaction): float
    {
        $percent = $this->ruleService->getCommissionPercent($client, $transaction);
        return ceil_plus($transaction->getAmount() * $percent / 100, 2);
    }
}