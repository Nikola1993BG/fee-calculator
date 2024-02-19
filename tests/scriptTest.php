<?php

namespace Tests;

use App\Calculator\Container;
use App\Calculator\CsvParser;
use PHPUnit\Framework\TestCase;
use App\Calculator\CalculatorFactory;
use App\Calculator\TransactionStorage;
use App\Calculator\ExchangeRatesProvider;
use App\Calculator\TransactionFeeCalculator;
use App\Calculator\Interfaces\DataProcessorInterface;
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

        $container = new Container();
        $container->set(DataProcessorInterface::class, fn() => new CsvParser($csvFile));
        $container->set(ExchangeRatesProviderInterface::class, fn() =>
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

        $calcFactory = new CalculatorFactory($container);

        // Create the necessary objects
        $transactionStorage = new TransactionStorage($container->get(DataProcessorInterface::class));

        // Calculate the fees
        $result = [];
        foreach ($transactionStorage->getAll() as $trns) {
            $t = reset($trns);
            $clientType = $t->client->type;
            $transactionFeeCalculator = $calcFactory->createCalculator($clientType);

            $result = array_merge($transactionFeeCalculator->calcFee($trns), $result);
        }

        // Sort the result by trn_id
        usort($result, fn($a, $b) => $a->trn_id - $b->trn_id);

        // Assert that the output matches the expected output
        $this->assertEquals($expectedOutput, array_column($result, 'commission'));
    }
}