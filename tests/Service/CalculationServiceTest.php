<?php

declare(strict_types=1);

namespace Tests;

use App\Model\Client;
use App\Model\Transaction;
use App\Service\CalculationService;
use PHPUnit\Framework\TestCase;

class CalculationServiceTest extends TestCase
{

    /**
     *
     * @dataProvider dataProviderForTransactionCommissionFeeCalc
     * @param Client $client
     * @param Transaction $transaction
     */
    public function testTransactionFeeCommissionCalculation(Client $client, Transaction $transaction, float $expected)
    {
        $calcService = new CalculationService([
            'USD' => 1.1497,
            'JPY' => 129.53,
        ], 'EUR');
        $commissionFee = $calcService->calculateCommissionFee($client, $transaction);

        $this->assertEquals($expected, $commissionFee);
    }

    public function dataProviderForTransactionCommissionFeeCalc(): array
    {
        return [
            [
                new Client(1, Client::TYPE_PRIVATE, []),
                new Transaction(Transaction::TYPE_WITHDRAW, 'EUR', 1200.00, new \DateTime('2014-12-31')),
                0.6
            ],
        ];
    }
}
