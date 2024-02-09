<?php

namespace App\Calculator;

use App\Calculator\Interfaces\DataProcessorInterface;

class TransactionStorage
{
    public function __construct(private DataProcessorInterface $processor)
    {
    }

    /**
     * Retrieves all transactions from the parser and organizes them into an associative array.
     *
     * @return array The organized transactions, grouped by client ID.
     */
    public function getAll(): array
    {
        $transactions = $this->processor->getAll();
        $trn_id = 0;
        $data = [];
        foreach ($transactions as $transaction) {
            if (!isset($clients[$transaction[1]])) {
                $clients[$transaction[1]] = new \App\Calculator\Client($transaction[1], $transaction[2]);
            }

            $data[$transaction[1]][$trn_id] = new \App\Calculator\Transaction(
                $trn_id,
                $transaction[0],
                $clients[$transaction[1]],
                $transaction[3],
                $transaction[4],
                $transaction[5]
            );

            $trn_id++;
        }
        return $data;
    }
}
