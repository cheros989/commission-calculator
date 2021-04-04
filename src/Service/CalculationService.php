<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Client;
use App\Model\Transaction;
use App\Service\Interfaces\ICalculationService;
use Carbon\Carbon;

class CalculationService implements ICalculationService
{
    const MAX_LIMIT_TRANSACTIONS_PER_WEEK = 3;
    const MAX_LIMIT_TRANSACTIONS_AMOUNT_PER_WEEK = 1000;
    const DEPOSIT_PERCENTAGE = 0.03;
    const BUSINESS_CLIENT_WITHDRAW_PERCENTAGE = 0.5;
    const PRIVATE_CLIENT_WITHDRAW_PERCENTAGE = 0.3;

    private array $rates;
    private string $baseCurrency;

    /**
     * CalculationService constructor.
     * @param array $rates according to the base currency
     * @param string $baseCurrency
     */
    public function __construct(array $rates, string $baseCurrency)
    {
        $this->rates = $rates;
        $this->baseCurrency = $baseCurrency;
    }

    public function calculateCommissionFee(Client $client, Transaction $transaction): float
    {
        $obtainAmount = $this->getObtainAmountFromTransaction($client, $transaction);
        $percentage = $this->getPercentageRateForTransaction($client, $transaction);

        return ceil_plus($obtainAmount * $percentage / 100, 2);
    }

    private function getObtainAmountFromTransaction(Client $client, Transaction $transaction): float
    {
        // For business client we always obtain all amount independent of other rules
        if ($client->getType() === Client::TYPE_BUSINESS) {
            return $transaction->getAmount();
        }

        // For deposit we also just obtain all amount by fixed percent
        if ($transaction->getType() === Transaction::TYPE_DEPOSIT) {
            return $transaction->getAmount();
        }

        $sameWeekTransactions = $this->getAllClientsWithdrawTransactionsForAWeek($client->getTransactions(), $transaction);
        // For transaction exceeded operations limit per week we also obtain all
        if (count($sameWeekTransactions) > self::MAX_LIMIT_TRANSACTIONS_PER_WEEK) {
            return $transaction->getAmount();
        }

        $totalAmountForSameWeek = array_reduce($sameWeekTransactions, function ($carry, Transaction $transaction) {
            $carry += $this->convertToBaseCurrency($transaction->getAmount(), $transaction->getCurrency());
            return $carry;
        });

        if ($totalAmountForSameWeek >= self::MAX_LIMIT_TRANSACTIONS_AMOUNT_PER_WEEK) {
            return $transaction->getAmount();
        }

        $currentTransactionAmount = $this->convertToBaseCurrency($transaction->getAmount(), $transaction->getCurrency());
        $totalAmount = $totalAmountForSameWeek + $currentTransactionAmount;
        // For transaction exceeded amount limit we should charge only exceeded amount
        if ($totalAmount > self::MAX_LIMIT_TRANSACTIONS_AMOUNT_PER_WEEK) {
            $exceededLimit = $totalAmount - self::MAX_LIMIT_TRANSACTIONS_AMOUNT_PER_WEEK;
            return $this->convertFromBaseCurrency($exceededLimit, $transaction->getCurrency());
        }

        // Otherwise just return 0 and let's make client happy
        return 0;
    }

    private function getPercentageRateForTransaction(Client $client, Transaction $transaction)
    {
        if ($transaction->getType() === Transaction::TYPE_DEPOSIT) {
            // Just always percentage for all deposits
            return self::DEPOSIT_PERCENTAGE;
        }

        // If code reach here then transaction is withdraw
        if ($client->getType() === Client::TYPE_BUSINESS) {
            return self::BUSINESS_CLIENT_WITHDRAW_PERCENTAGE;
        }

        if ($client->getType() === Client::TYPE_PRIVATE) {
            return self::PRIVATE_CLIENT_WITHDRAW_PERCENTAGE;
        }
    }

    private function getAllClientsWithdrawTransactionsForAWeek(array $clientTransactions, Transaction $currentTransaction): array
    {
        // use Carbon, it's handy
        $currentTransactionDate = Carbon::make($currentTransaction->getDate());

        return array_filter($clientTransactions, function (Transaction $transaction) use ($currentTransactionDate) {
            // We should count only WITHDRAW transactions
            return $transaction->getType() === Transaction::TYPE_WITHDRAW &&
                $currentTransactionDate->isSameWeek($transaction->getDate());
        });
    }

    private function convertToBaseCurrency(float $amount, string $transactionCurrency): float
    {
        return $transactionCurrency === $this->baseCurrency
            ? $amount
            : $amount / $this->rates[$transactionCurrency];
    }

    private function convertFromBaseCurrency(float $amount, string $transactionCurrency): float
    {
        return $transactionCurrency === $this->baseCurrency
            ? $amount
            : $amount * $this->rates[$transactionCurrency];
    }
}
