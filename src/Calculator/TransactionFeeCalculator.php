<?php

namespace App\Calculator;

use App\Calculator\Interfaces\ExchangeRatesProviderInterface;
use App\Calculator\Interfaces\TransactionFeeCalculatorInterface;

class TransactionFeeCalculator implements TransactionFeeCalculatorInterface
{
    public function __construct(
        private TransactionFeeCalculatorInterface $privateCalc,
        private TransactionFeeCalculatorInterface $businessCalc
    ) {
    }

    public function calcFee(array $transactions): array
    {
        $transaction = reset($transactions);
        if ($transaction->client->type == 'private') {
            return $this->privateCalc->calcFee($transactions);
        } elseif ($transaction->client->type == 'business') {
            return $this->businessCalc->calcFee($transactions);
        } else {
            throw new \Exception('Invalid client type');
        }
    }
}
