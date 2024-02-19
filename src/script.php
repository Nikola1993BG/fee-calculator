<?php

require '../vendor/autoload.php';

use App\Calculator\Container;
use App\Calculator\CsvParser;
use App\Calculator\TransactionStorage;
use App\Calculator\ExchangeRatesProvider;
use App\Calculator\TransactionFeeCalculator;
use App\Calculator\TransactionFeePrivateCalculator;
use App\Calculator\TransactionFeeBusinessCalculator;
use App\Calculator\Interfaces\DataProcessorInterface;
use App\Calculator\Interfaces\ExchangeRatesProviderInterface;
use App\Calculator\Interfaces\TransactionFeeCalculatorInterface;

Container::set(DataProcessorInterface::class, fn() => new CsvParser($argv[1]));
Container::set(ExchangeRatesProviderInterface::class, fn() => new ExchangeRatesProvider());
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

$transactionFeeCalculator = Container::get(TransactionFeeCalculatorInterface::class);
$result = [];
foreach ((new TransactionStorage(Container::get(DataProcessorInterface::class)))->getAll() as $trns) {
    $result = array_merge($transactionFeeCalculator->calcFee($trns), $result);
}

usort($result, fn($a, $b) => $a->trn_id - $b->trn_id);

foreach ($result as $transaction) {
    echo $transaction->commission . PHP_EOL;
}
