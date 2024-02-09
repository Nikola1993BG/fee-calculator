<?php

namespace App\Calculator;

class Client
{
    public const LIMIT = 1000;
    public function __construct(
        public int $id,
        public string $type,
        public float $remaining_amount = self::LIMIT,
        public ?string $last_transaction = null
    ) {
    }
}
