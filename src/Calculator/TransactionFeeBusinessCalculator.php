<?php

namespace App\Calculator;

use App\Calculator\Interfaces\ExchangeRatesProviderInterface;
use App\Calculator\Interfaces\TransactionFeeCalculatorInterface;

class TransactionFeeBusinessCalculator implements TransactionFeeCalculatorInterface
{
    private array $commission_fees = ['deposit' => 0.0003,'withdraw' => 0.005];
    public function __construct(private ExchangeRatesProviderInterface $exchangeRatesProvider)
    {
    }
    public function calcFee(array $transactions): array
    {
        foreach ($transactions as $transaction) {
            $commission_fee = $this->commission_fees[$transaction->type];

            $currency = $transaction->currency;
            $rate = $this->exchangeRatesProvider::getRate($currency);
            $euro_amount = $transaction->amount / $rate;

            $fee = $euro_amount * $commission_fee * $rate;
            if ($transaction->currency == 'JPY') {
                $fee =  ceil($fee);
                $transaction->commission = $fee;
            } elseif ($fee > 0.1) {
                $transaction->commission = round($fee, 1);
            } else {
                $transaction->commission = round($fee, 2);
            }
        }
        return $transactions;
    }
}
