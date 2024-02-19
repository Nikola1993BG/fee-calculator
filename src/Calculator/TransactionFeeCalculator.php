<?php

namespace App\Calculator;

use App\Calculator\Interfaces\TransactionFeeCalculatorInterface;

class TransactionFeeCalculator implements TransactionFeeCalculatorInterface
{
    public function __construct(
        private TransactionFeeCalculatorInterface $privateCalc,
        private TransactionFeeCalculatorInterface $businessCalc
    ) {
    }

    /**
     * Calculates the fee for an array of transactions.
     *
     * @param array $transactions An array of transactions.
     * @return array The calculated fees for each transaction.
     * @throws \Exception If the client type is invalid.
     */
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
