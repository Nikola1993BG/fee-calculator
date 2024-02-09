<?php

namespace App\Calculator;

use App\Calculator\Interfaces\TransactionFeeCalculatorInterface;
use App\Calculator\Interfaces\ExchangeRatesProviderInterface;

class TransactionFeeCalculator implements TransactionFeeCalculatorInterface
{
    private array $commission_fees_private = ['deposit' => 0.0003,'withdraw' => 0.003];
    private array $commission_fees_business = ['deposit' => 0.0003,'withdraw' => 0.005];
    public function __construct(private ExchangeRatesProviderInterface $rates)
    {
    }

    /**
     * Calculates the transaction fee for each transaction in the given array.
     *
     * @param array $transactions The array of transactions.
     * @return array The updated array of transactions with calculated fees.
     */
    public function calcFee($transactions): array
    {
        foreach ($transactions as $transaction) {
            if ($transaction->client->type == 'private') {
                $commission_fee = $this->commission_fees_private[$transaction->type];
            } else {
                $commission_fee = $this->commission_fees_business[$transaction->type];
            }

            $currency = $transaction->currency;
            $rate = $this->rates::getRate($currency);
            $euro_amount = $transaction->amount / $rate;

            if ($transaction->type == 'withdraw' && $transaction->client->type == 'private') {
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
