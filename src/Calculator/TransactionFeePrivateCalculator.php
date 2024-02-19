<?php

namespace App\Calculator;

use App\Calculator\Interfaces\ExchangeRatesProviderInterface;
use App\Calculator\Interfaces\TransactionFeeCalculatorInterface;

class TransactionFeePrivateCalculator implements TransactionFeeCalculatorInterface
{
    private array $commission_fees = ['deposit' => 0.0003,'withdraw' => 0.003];
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

            if ($transaction->type == 'withdraw') {
                if ($transaction->client->last_transaction != null) {
                    $date = new \DateTime($transaction->client->last_transaction);
                    $date2 = new \DateTime($transaction->date);

                    $start = clone $date;
                    $start->modify('monday this week');
                    $end = clone $date;
                    $end->modify('sunday this week');

                    $result = $date2 >= $start;
                    $result1 = $date2 <= $end;

                    if (!$result || !$result1) {
                        $transaction->client->remaining_amount = Client::LIMIT;
                    }
                }

                $transaction->client->last_transaction = $transaction->date;

                if ($euro_amount < $transaction->client->remaining_amount) {
                    $transaction->client->remaining_amount = $transaction->client->remaining_amount - $euro_amount;
                    $euro_amount = 0;
                } else {
                    $euro_amount = $euro_amount - $transaction->client->remaining_amount;
                    $transaction->client->remaining_amount = 0;
                }
            }

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
