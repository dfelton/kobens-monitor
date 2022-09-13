<?php

declare(strict_types=1);

require \dirname(__DIR__).'/src/bootstrap.php';

set_error_handler(function(int $errNo, string $errStr, string $errFile, int $errLine, array $errContext): void {
    return;
});

use Kobens\Monitor\TradeRepeater\Orders;
use Kobens\Math\BasicCalculator\Add;
use Kobens\Math\BasicCalculator\Multiply;
use Kobens\Math\BasicCalculator\Divide;
use Kobens\Math\BasicCalculator\Subtract;

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

function addData(array &$data): void {
    foreach ($data as &$arr) {

        $quoteMeta = getQuoteMeta(
            $arr['buy_amount'],
            $arr['buy_price'],
            $arr['sell_amount'],
            $arr['sell_price']
        );

        $buySellDiff = Subtract::getResult((string) $arr['sell_price'], (string) $arr['buy_price']);
        $buySellPercentGain = bcmul(bcdiv($buySellDiff, (string) $arr['buy_price'], 5), '100', 3);
        $buySellPercentGain = rtrim(rtrim($buySellPercentGain, '0'), '.');
        $arr['sell_price_increase'] = $buySellDiff;
        $arr['sell_price_increase_percent'] = $buySellPercentGain . '%';
        $arr['profit_base'] = Subtract::getResult($arr['buy_amount'], $arr['sell_amount']);

        $arr['sell_quote_subtotal'] = $quoteMeta['sell_quote_subtotal'];
        $arr['sell_quote_fees'] = $quoteMeta['sell_quote_fees'];
        $arr['sell_quote_total'] = $quoteMeta['sell_quote_total'];
        $arr['profit_quote'] = $quoteMeta['profit_quote'];
    }
}

function getQuoteMeta(string $buyAmount, string $buyPrice, string $sellAmount, string $sellPrice, string $assumedFeeRate = '0.001'): array
{
    $buyQuoteSubtotal = Multiply::getResult($buyAmount, $buyPrice);
    $buyQuoteFees = Multiply::getResult($buyQuoteSubtotal, $assumedFeeRate);
    $buyQuoteTotal = Add::getResult($buyQuoteSubtotal, $buyQuoteFees);

    $sellQuoteSubtotal = Multiply::getResult($sellAmount, $sellPrice);
    $sellQuoteFees = Multiply::getResult($sellQuoteSubtotal, $assumedFeeRate);
    $sellQuoteTotal = Subtract::getResult($sellQuoteSubtotal, $sellQuoteFees);

    return [
        'buy_quote_subtotal' => $buyQuoteSubtotal,
        'buy_quote_fees' => $buyQuoteFees,
        'buy_quote_total' => $buyQuoteTotal,
        'sell_quote_subtotal' => $buyQuoteSubtotal,
        'sell_quote_fees' => $buyQuoteFees,
        'sell_quote_total' => $buyQuoteTotal,
        'profit_quote' => Subtract::getResult($sellQuoteTotal, $buyQuoteTotal),
    ];
}

function getPrice(string $symbol): string {
    $handle = curl_init('https://api.gemini.com/v1/pubticker/' . $symbol);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
    $body = curl_exec($handle);
    $data = json_decode(is_string($body) ? $body : '{}', true);
    return $data['bid'] ?? '0';
}

$meta = [
    'total_orders' => 0,
    'total_buy_orders' => 0,
    'total_buy_usd' => '0',
    'total_sell_orders' => 0,
    'total_sell_usd' => '0',
    'total_base_purchased' => '0',
];
$data = [];

$priceMax = (int) ($_GET['price_max'] ?? '0');
$priceMin = (int) ($_GET['price_min'] ?? '0');
foreach ((new Orders())->getOrders($_GET['symbol'] ?? 'btcusd') as $order) {

    if (
        ($priceMin && Compare::getResult($order['buy_price'], $priceMin) !== Compare::LEFT_GREATER_THAN) ||
        ($priceMax && Compare::getResult($order['sell_price'], $priceMax) !== Compare::LEFT_LESS_THAN)
    ) {
        continue;
    }

    $data[$order['status']][] = [
        'id' => $order['id'],
        'amount' => $order['amount'],
        'price' => $order['price'],
        'buy_amount' => $order['buy_amount'],
        'buy_price' => $order['buy_price'],
        'sell_amount' => $order['sell_amount'],
        'sell_price' => $order['sell_price'],
        'color' => $order['color'],
    ];
    if ($order['status'] === 'BUY_PLACED') {
        ++$meta['total_orders'];
        ++$meta['total_buy_orders'];
        $buyQuoteAmount = Multiply::getResult(
            $order['buy_amount'],
            $order['average_buy_price']
        );
        $meta['total_buy_usd'] = Add::getResult(
            $meta['total_buy_usd'],
            Add::getResult(
                $buyQuoteAmount,
                Multiply::getResult($buyQuoteAmount, '0.0035')
            )
        );
    } elseif ($order['status'] === 'SELL_PLACED') {
        ++$meta['total_orders'];
        ++$meta['total_sell_orders'];

        $meta['total_sell_usd'] = Add::getResult(
            $meta['total_sell_usd'],
            Multiply::getResult(
                $order['average_buy_price'],
                $order['buy_amount'],
            ),
        );
        $meta['total_base_purchased'] = Add::getResult(
            $meta['total_base_purchased'],
            $order['buy_amount']
        );
    }
}

$meta['average_base_price'] = $meta['total_sell_orders'] === 0 ? '0' : Divide::getResult($meta['total_sell_usd'], $meta['total_base_purchased'], 4);
$meta['base_symbol'] = substr($_GET['symbol'] ?? 'btcusd', 0, -3);
$meta['base_current_value'] = Multiply::getResult(
    $meta['total_base_purchased'],
    getPrice($_GET['symbol'] ?? 'btcusd')
);


ksort($data);
foreach ($data as &$status) {
    addData($status);
}

\header('content-type:application/json');
echo \json_encode([
    'meta' => $meta,
    'orders' => $data,
]);
