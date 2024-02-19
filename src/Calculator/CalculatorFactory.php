<?php

namespace App\Calculator;

use App\Calculator\ExchangeRatesProvider;
use App\Calculator\Interfaces\ExchangeRatesProviderInterface;
use App\Calculator\TransactionFeePrivateCalculator;
use App\Calculator\TransactionFeeBusinessCalculator;
use App\Calculator\Interfaces\TransactionFeeCalculatorInterface;

class CalculatorFactory
{
    public function __construct(private Container $container)
    {
    }
    public function createCalculator(string $type): ?TransactionFeeCalculatorInterface
    {
        if ($type == 'private') {
            return new TransactionFeePrivateCalculator($this->container->get(ExchangeRatesProviderInterface::class));
        } elseif ($type == 'business') {
            return new TransactionFeeBusinessCalculator($this->container->get(ExchangeRatesProviderInterface::class));
        }
    }
}
