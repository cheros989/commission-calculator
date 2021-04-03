<?php

declare(strict_types=1);

namespace App\Model;


class Transaction
{
    const TYPE_WITHDRAW = 'withdraw';
    const TYPE_DEPOSIT = 'deposit';

    private string $type;
    private string $currency;
    private float $amount;
    private \DateTime $date;

    public function __construct(string $type, string $currency, float $amount, \DateTime $date)
    {
        $this->type = $type;
        $this->currency = $currency;
        $this->amount = $amount;
        $this->date = $date;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }
}