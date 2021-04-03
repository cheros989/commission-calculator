<?php


namespace App\Service;


use App\Model\Client;
use App\Model\Transaction;
use App\Service\Interfaces\IRuleService;
use Carbon\Carbon;

class RuleService implements IRuleService
{
    const MAX_LIMIT_TRANSACTIONS_PER_WEEK = 3;
    const MAX_LIMIT_TRANSACTIONS_AMOUNT_PER_WEEK_IN_EUR = 1000;

    public function getCommissionPercent(Client $client, Transaction $transaction): float
    {
        if ($this->isFreeTransaction($client, $transaction)) {
            return 0;
        }
        return 0.03;
    }

    private function isFreeTransaction(Client $client, Transaction $transaction): bool
    {
        $sameWeekTransactions = $this->getAllClientsTransactionsForAWeek($client->getTransactions(), $transaction->getDate());

        if (count($sameWeekTransactions) > self::MAX_LIMIT_TRANSACTIONS_PER_WEEK) {
            return false;
        }

        // todo: calculate amount
    }

    private function getAllClientsTransactionsForAWeek(array $clientTransactions, \DateTime $currentTransactionDate): array
    {
        // use Carbon, it's handy
        $currentTransactionDate = Carbon::make($currentTransactionDate);

        return array_filter($clientTransactions, function (Transaction $transaction) use ($currentTransactionDate) {
            return $currentTransactionDate->isSameWeek($transaction->getDate());
        });
    }
}