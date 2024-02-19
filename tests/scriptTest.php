<?php

namespace Tests;

use App\Calculator\Container;
use App\Calculator\CsvParser;
use PHPUnit\Framework\TestCase;
use App\Calculator\TransactionStorage;
use App\Calculator\TransactionFeeCalculator;
use App\Calculator\TransactionFeePrivateCalculator;
use App\Calculator\TransactionFeeBusinessCalculator;
use App\Calculator\Interfaces\DataProcessorInterface;
use App\Calculator\Interfaces\ExchangeRatesProviderInterface;
use App\Calculator\Interfaces\TransactionFeeCalculatorInterface;

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

        Container::set(DataProcessorInterface::class, fn() => new CsvParser($csvFile));
        Container::set(ExchangeRatesProviderInterface::class, fn() =>
            new class implements ExchangeRatesProviderInterface
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
            });

            Container::set(TransactionFeePrivateCalculator::class, fn() => new TransactionFeePrivateCalculator(
                Container::get(ExchangeRatesProviderInterface::class)
            ));
            Container::set(TransactionFeeBusinessCalculator::class, fn() => new TransactionFeeBusinessCalculator(
                Container::get(ExchangeRatesProviderInterface::class)
            ));
            Container::set(TransactionFeeCalculatorInterface::class, fn() => new TransactionFeeCalculator(
                Container::get(TransactionFeePrivateCalculator::class),
                Container::get(TransactionFeeBusinessCalculator::class)
            ));

        // Create the necessary objects
        $transactionStorage = new TransactionStorage(Container::get(DataProcessorInterface::class));
        $transactionFeeCalculator = Container::get(TransactionFeeCalculatorInterface::class);
        // Calculate the fees
        $result = [];
        foreach ($transactionStorage->getAll() as $trns) {
            $result = array_merge($transactionFeeCalculator->calcFee($trns), $result);
        }

        // Sort the result by trn_id
        usort($result, fn($a, $b) => $a->trn_id - $b->trn_id);

        // Assert that the output matches the expected output
        $this->assertEquals($expectedOutput, array_column($result, 'commission'));
    }
}
