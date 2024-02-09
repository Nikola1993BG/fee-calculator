<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Calculator\TransactionStorage;
use App\Calculator\CsvParser;
use App\Calculator\TransactionFeeCalculator;
use App\Calculator\Interfaces\ExchangeRatesProviderInterface;

class ScriptTest extends TestCase
{
    public function testScriptOutput()
    {
        // Set up the test data
        $csvFile = './src/input.csv';
        $expectedOutput = [
            '0.60',
            '3.00',
            '0.00',
            '0.06',
            '1.50',
            '0.00',
            '0.70',
            '0.30',
            '0.30',
            '3.00',
            '0.00',
            '0.00',
            '8612'
        ];

        // Create the necessary objects
        $transactionStorage = new TransactionStorage(new CsvParser($csvFile));

        $rates = new class implements ExchangeRatesProviderInterface
        {
            public static function getRate($currency): float
            {
                $rates = [
                    'EUR' => 1,
                    'USD' => 1.1497,
                    'JPY' => 129.53
                ];
                return $rates[$currency];
            }
        };

        $transactionFeeCalculator = new TransactionFeeCalculator($rates);

        // Calculate the fees
        $result = [];
        foreach ($transactionStorage->getAll() as $transaction) {
            $result = array_merge($transactionFeeCalculator->calcFee($transaction), $result);
        }

        // Sort the result by trn_id
        usort($result, fn($a, $b) => $a->trn_id - $b->trn_id);

        // Assert that the output matches the expected output
        $this->assertEquals($expectedOutput, array_column($result, 'commission'));
    }
}
