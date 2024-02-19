<?php

namespace App\Calculator;

use App\Calculator\Interfaces\ExchangeRatesProviderInterface;
use App\Calculator\TransactionFeePrivateCalculator;
use App\Calculator\TransactionFeeBusinessCalculator;
use App\Calculator\Interfaces\TransactionFeeCalculatorInterface;

class CalculatorFactory
{
    public function __construct()
    {
    }
    /**
     * Creates a transaction fee calculator based on the given type.
     *
     * @param string $type The type of calculator to create ('private' or 'business').
     * @return TransactionFeeCalculatorInterface|null The created calculator instance, or null if the type is invalid.
     */
    public static function createCalculator(string $type): ?TransactionFeeCalculatorInterface
    {
        if ($type == 'private') {
            return new TransactionFeePrivateCalculator(Container::get(ExchangeRatesProviderInterface::class));
        } elseif ($type == 'business') {
            return new TransactionFeeBusinessCalculator(Container::get(ExchangeRatesProviderInterface::class));
        }
    }
}
