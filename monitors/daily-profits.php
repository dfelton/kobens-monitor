<?php

declare(strict_types=1);

require \dirname(__DIR__).'/src/bootstrap.php';

$dailyProfits = new \Kobens\Monitor\TradeRepeater\DailyProfits();

\header('content-type:application/json');
echo \json_encode($dailyProfits->getData());
