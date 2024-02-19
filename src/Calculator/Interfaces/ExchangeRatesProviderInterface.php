<?php

namespace App\Calculator\Interfaces;

interface ExchangeRatesProviderInterface
{
    public static function getRate(string $currency): float;
}
