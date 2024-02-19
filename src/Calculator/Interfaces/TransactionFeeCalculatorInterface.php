<?php

namespace App\Calculator\Interfaces;

interface TransactionFeeCalculatorInterface
{
    public function calcFee(array $transactions): array;
}
