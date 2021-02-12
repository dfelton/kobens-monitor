<?php

declare(strict_types=1);

require \dirname(__DIR__).'/src/bootstrap.php';


set_error_handler(function(int $errNo, string $errStr, string $errFile, int $errLine, array $errContext): void {
    return;
});

use Kobens\Monitor\TradeRepeater\Orders;

$orders = new Orders();
$data = [];

function getInt(string $price): int {
    $price = (string) $price;
    if (strpos($price, '.') !== false) {
        $price = explode('.', $price);
        $price[1] = str_pad($price[1], 8, '0', STR_PAD_RIGHT);
        $price = $price[0] . $price[1];
    } else {
        $price .= '00000000';
    }
    return (int) $price;
}

function getString(int $price): string {
    $price = (string) $price;
    if (strlen($price) < 8) {
        $price = str_pad($price, 8, '0', STR_PAD_LEFT);
    }
    $fraction = substr($price, -8);
    if (strlen($price) > 8) {
        $whole = substr($price, 0, -8);
    } else {
        $whole = '0';
    }
    return $fraction !== '00000000'
        ? $whole . '.' . rtrim($fraction, '0')
        : $whole;
}

function addData(&$data): void {
    $priorPrice = null;
    foreach (array_keys($data) as $i) {
        if ($priorPrice === null) {
            $data[$i]['buy_price_increase'] = 'N/A';
        } else {
            $diff = $i - $priorPrice;
            $percentIncrease = bcmul(bcdiv((string) $diff, (string) $priorPrice, 5), '100', 3);

            $data[$i]['buy_price_increase'] = getString($diff);
            $data[$i]['buy_price_increase_percent'] = $percentIncrease . '%';
        }
        $priorPrice = $i;

        $buySellDiff = bcsub((string) $data[$i]['sell_price'], (string) $data[$i]['buy_price'], 8);
        $buySellPercentGain = bcmul(bcdiv($buySellDiff, (string) $data[$i]['buy_price'], 5), '100', 3);
        $buySellDiff = rtrim(rtrim($buySellDiff, '0'), '.');
        $buySellPercentGain = rtrim(rtrim($buySellPercentGain, '0'), '.');
        $data[$i]['sell_price_increase'] = $buySellDiff;
        $data[$i]['sell_price_increase_percent'] = $buySellPercentGain . '%';

        $buyAmount = (string) $data[$i]['buy_amount'];
        $sellAmount = (string) $data[$i]['sell_amount'];
        $saveAmount = bcsub($buyAmount, $sellAmount, 8);

        $data[$i]['save_amount'] = rtrim(rtrim($saveAmount, '0'), '.');
    }
}

foreach ($orders->getOrders() as $order) {
    $symbol = $order['symbol'];
    $side = $order['side'];

    if (($data[$symbol] ?? null) === null) {
        $data[$symbol] = [];
    }
    if (($data[$symbol][$side] ?? null) === null) {
        $data[$symbol][$side] = [];
    }
    $data[$symbol][$side][getInt($order['buy_price'])] = [
        'id' => $order['id'],
        'amount' => $order['amount'],
        'price' => $order['price'],
        'buy_amount' => $order['buy_amount'],
        'buy_price' => $order['buy_price'],
        'sell_amount' => $order['sell_amount'],
        'sell_price' => $order['sell_price'],
    ];
}

ksort($data);

foreach (array_keys($data) as $symbol) {
    if ($data[$symbol]['buy'] ?? null) {
        ksort($data[$symbol]['buy']);
        addData($data[$symbol]['buy']);
    }
    if ($data[$symbol]['sell'] ?? null) {
        ksort($data[$symbol]['sell']);
        addData($data[$symbol]['sell']);
    }
}


\header('content-type:application/json');
echo \json_encode(['gemini' => $data]);
