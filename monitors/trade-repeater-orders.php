<?php

require __DIR__.'/bootstrap.php';

use Kobens\Monitor\TradeRepeater\Orders;

$orders = new Orders();
$data = [];

foreach ($orders->getOrders() as $order) {
    $exchange = $order['exchange'];
    $symbol = $order['symbol'];
    $side = $order['side'];

    if (!\array_key_exists($exchange, $data)) {
        $data[$exchange] = [];
    }
    if (!\array_key_exists($symbol, $data[$exchange])) {
        $data[$exchange][$symbol] = [];
    }
    if (!\array_key_exists($side, $data[$exchange][$symbol])) {
        $data[$exchange][$symbol][$side] = [];
    }
    $data[$exchange][$symbol][$side][] = [
        'amount' => $order['amount'],
        'price' => $order['price'],
        'buy_amount' => $order['buy_amount'],
        'buy_price' => $order['buy_price'],
        'sell_amount' => $order['sell_amount'],
        'sell_price' => $order['sell_price'],
    ];
}

\header('content-type:application/json');
echo \json_encode($data);
