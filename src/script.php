<?php

require '../vendor/autoload.php';

use App\Calculator\Container;
use App\Calculator\CsvParser;
use App\Calculator\CalculatorFactory;
use App\Calculator\TransactionStorage;
use App\Calculator\ExchangeRatesProvider;
use App\Calculator\Interfaces\DataProcessorInterface;
use App\Calculator\Interfaces\ExchangeRatesProviderInterface;

$container = new Container();
$container->set(DataProcessorInterface::class, fn() => new CsvParser($argv[1]));
$container->set(ExchangeRatesProviderInterface::class, fn() => new ExchangeRatesProvider());

$calcFactory = new CalculatorFactory($container);

$result = [];
foreach ((new TransactionStorage($container->get(DataProcessorInterface::class)))->getAll() as $trns) {
    $t = reset($trns);
    $clientType = $t->client->type;
    $calc = $calcFactory->createCalculator($clientType);
    $result = array_merge($calc->calcFee($trns), $result);
}

usort($result, fn($a, $b) => $a->trn_id - $b->trn_id);

foreach ($result as $transaction) {
    echo $transaction->commission . PHP_EOL;
}
