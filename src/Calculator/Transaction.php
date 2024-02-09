<?php

namespace App\Calculator;

class Transaction
{
    public function __construct(
        public int $trn_id,
        public string $date,
        public Client $client,
        public string $type,
        public int $amount,
        public string $currency,
        public ?float $commission = null
    ) {
    }
}
