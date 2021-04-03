<?php

declare(strict_types=1);

namespace App\Model;


/**
 * Class Client
 * @package App\Model
 */
class Client
{
    const TYPE_BUSINESS = 'business';
    const TYPE_PRIVATE = 'private';

    private int $id;
    private string $type;
    private array $transactions;

    public function __construct(int $id, string $type, array $transactions = [])
    {
        $this->id = $id;
        $this->type = $type;
        $this->transactions = $transactions;
    }

    public function addTransaction(Transaction $transaction)
    {
        $this->transactions[] = $transaction;
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getId(): int
    {
        return $this->id;
    }
}