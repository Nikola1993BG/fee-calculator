<?php

namespace App\Calculator;

use App\Calculator\Interfaces\ExchangeRatesProviderInterface;

class ExchangeRatesProvider implements ExchangeRatesProviderInterface
{
    private const URL = 'https://developers.paysera.com/tasks/api/currency-exchange-rates';
    private static ?array $rates = null;

    /**
     * Retrieves the exchange rate for a given currency.
     *
     * @param string $currency The currency code.
     * @return float The exchange rate for the specified currency.
     */
    public static function getRate(string $currency): float
    {
        if (!isset(self::$rates)) {
            self::$rates = self::getRates();
        }
        return self::$rates[$currency];
    }

    /**
     * Retrieves the exchange rates from the API.
     *
     * @return array The exchange rates as an associative array.
     */
    private static function getRates(): array
    {
        $ch = curl_init(self::URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return (array) json_decode($response)->rates;
    }
}
