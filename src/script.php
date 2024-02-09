<?php

require '../vendor/autoload.php';

use App\Calculator\TransactionStorage;
use App\Calculator\CsvParser;
use App\Calculator\TransactionFeeCalculator;
use App\Calculator\ExchangeRatesProvider;

$result = [];
foreach ((new TransactionStorage(new CsvParser($argv[1])))->getAll() as $trns) {
    $calc = new TransactionFeeCalculator(new ExchangeRatesProvider());
    $result = array_merge($calc->calcFee($trns), $result);
}

usort($result, fn($a, $b) => $a->trn_id - $b->trn_id);

foreach ($result as $transaction) {
    echo $transaction->commission . PHP_EOL;
}
