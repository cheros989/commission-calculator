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
            // 2014-12-31,4,private,withdraw,1200.00,EUR
            [
                new Client(4, Client::TYPE_PRIVATE, []),
                new Transaction(Transaction::TYPE_WITHDRAW, 'EUR', 1200.00, new \DateTime('2014-12-31')),
                0.6
            ],
            // 2015-01-01,4,private,withdraw,1000.00,EUR
            [
                new Client(4, Client::TYPE_PRIVATE, [
                    new Transaction(Transaction::TYPE_WITHDRAW, 'EUR', 1200.00, new \DateTime('2014-12-31')),
                ]),
                new Transaction(Transaction::TYPE_WITHDRAW, 'EUR', 1000.00, new \DateTime('2015-01-01')),
                3.00
            ],
            // 2016-01-05,4,private,withdraw,1000.00,EUR
            [
                new Client(4, Client::TYPE_PRIVATE, [
                    new Transaction(Transaction::TYPE_WITHDRAW, 'EUR', 1200.00, new \DateTime('2014-12-31')),
                    new Transaction(Transaction::TYPE_WITHDRAW, 'EUR', 1000.00, new \DateTime('2015-01-01'))
                ]),
                new Transaction(Transaction::TYPE_WITHDRAW, 'EUR', 1000.00, new \DateTime('2016-01-05')),
                0
            ],
            // 2016-01-05,1,private,deposit,200.00,EUR
            [
                new Client(1, Client::TYPE_PRIVATE, []),
                new Transaction(Transaction::TYPE_DEPOSIT, 'EUR', 200.00, new \DateTime('2016-01-05')),
                0.06
            ],
            // 2016-01-06,2,business,withdraw,300.00,EUR
            [
                new Client(2, Client::TYPE_BUSINESS, []),
                new Transaction(Transaction::TYPE_WITHDRAW, 'EUR', 300.00, new \DateTime('2016-01-06')),
                1.50
            ],
            // 2016-01-06,1,private,withdraw,30000,JPY
            [
                new Client(1, Client::TYPE_PRIVATE, []),
                new Transaction(Transaction::TYPE_WITHDRAW, 'JPY', 30000, new \DateTime('2016-01-06')),
                0
            ],
            // 2016-01-07,1,private,withdraw,1000.00,EUR
            [
                new Client(1, Client::TYPE_PRIVATE, [
                    new Transaction(Transaction::TYPE_WITHDRAW, 'JPY', 30000, new \DateTime('2016-01-06')),
                ]),
                new Transaction(Transaction::TYPE_WITHDRAW, 'EUR', 1000.00, new \DateTime('2016-01-07')),
                0.70
            ],
            // 2016-01-07,1,private,withdraw,100.00,USD
            [
                new Client(1, Client::TYPE_PRIVATE, [
                    new Transaction(Transaction::TYPE_WITHDRAW, 'JPY', 30000, new \DateTime('2016-01-06')),
                    new Transaction(Transaction::TYPE_WITHDRAW, 'EUR', 1000.00, new \DateTime('2016-01-07')),
                ]),
                new Transaction(Transaction::TYPE_WITHDRAW, 'USD', 100.00, new \DateTime('2016-01-07')),
                0.30
            ],
            // 2016-01-10,1,private,withdraw,100.00,EUR
            [
                new Client(1, Client::TYPE_PRIVATE, [
                    new Transaction(Transaction::TYPE_WITHDRAW, 'JPY', 30000, new \DateTime('2016-01-06')),
                    new Transaction(Transaction::TYPE_WITHDRAW, 'EUR', 1000.00, new \DateTime('2016-01-07')),
                    new Transaction(Transaction::TYPE_WITHDRAW, 'USD', 100.00, new \DateTime('2016-01-07')),
                ]),
                new Transaction(Transaction::TYPE_WITHDRAW, 'EUR', 100.00, new \DateTime('2016-01-10')),
                0.30
            ],
            // 2016-01-10,2,business,deposit,10000.00,EUR
            [
                new Client(2, Client::TYPE_BUSINESS, [
                    new Transaction(Transaction::TYPE_WITHDRAW, 'EUR', 300.00, new \DateTime('2016-01-06')),
                ]),
                new Transaction(Transaction::TYPE_DEPOSIT, 'EUR', 10000.00, new \DateTime('2016-01-10')),
                3.00
            ],
            // 2016-01-10,3,private,withdraw,1000.00,EUR
            [
                new Client(3, Client::TYPE_PRIVATE, []),
                new Transaction(Transaction::TYPE_WITHDRAW, 'EUR', 1000.00, new \DateTime('2016-01-10')),
                0.00
            ],
            // 2016-02-15,1,private,withdraw,300.00,EUR
            [
                new Client(1, Client::TYPE_PRIVATE, [
                    new Transaction(Transaction::TYPE_WITHDRAW, 'JPY', 30000, new \DateTime('2016-01-06')),
                    new Transaction(Transaction::TYPE_WITHDRAW, 'EUR', 1000.00, new \DateTime('2016-01-07')),
                    new Transaction(Transaction::TYPE_WITHDRAW, 'USD', 100.00, new \DateTime('2016-01-07')),
                    new Transaction(Transaction::TYPE_WITHDRAW, 'EUR', 100.00, new \DateTime('2016-02-15'))
                ]),
                new Transaction(Transaction::TYPE_WITHDRAW, 'EUR', 300.00, new \DateTime('2016-02-15')),
                0.00
            ],
            // 2016-02-19,5,private,withdraw,3000000,JPY
            [
                new Client(5, Client::TYPE_PRIVATE, []),
                new Transaction(Transaction::TYPE_WITHDRAW, 'JPY', 3_000_000.00, new \DateTime('2016-01-10')),
                8612
            ],
        ];
    }
}
