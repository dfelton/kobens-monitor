<?php

declare(strict_types=1);

require __DIR__ . '/src/bootstrap.php';

set_error_handler(function(int $errNo, string $errStr, string $errFile, int $errLine, array $errContext): void {
    return;
});

use Kobens\Gemini\Exchange\Currency\Pair;
use Kobens\Monitor\TradeRepeater\Orders;
use Kobens\Math\BasicCalculator\Add;
use Kobens\Math\BasicCalculator\Multiply;

$data = [];
foreach (Pair::getAllInstances() as $pair) {
    foreach ((new Orders())->getOrders($pair->getSymbol()) as $order) {
        if (($data[$pair->getSymbol()] ?? null) === null) {
            $data[$pair->getSymbol()] = '0';
        }
        if ($order['status'] === 'BUY_PLACED') {
            $amount = Multiply::getResult(
                $order['buy_amount'],
                $order['buy_price']
            );

            $data[$pair->getSymbol()] = (float) Add::getResult(
                (string) $data[$pair->getSymbol()],
                Add::getResult(
                    $amount,
                    Multiply::getResult(
                        $amount,
                        '0.0035'
                    )
                )
            );
        }
    }
}

$zero = [];
$positive = [];

foreach ($data as $symbol => $amount) {
    if ($amount > 0) {
        $positive[$symbol] = $amount;
    } else {
        $zero[$symbol] = $amount;
    }
}

ksort($zero);
asort($positive);
?>
<html>
<head>
<link rel="stylesheet" href="css/styles.css"/>
<style type="text/css">tr:hover{background:#808080}</style>
</head>
<body>
    <h2>Buy Order Quote Totals</h2>
    <table class="data-table">
    <?php foreach ($zero as $symbol => $info): ?>
    <tr>
        <td><?= $symbol ?></td>
        <td><?= $info ?></td>
    </tr>
    <?php endforeach ?>
    <?php foreach ($positive as $symbol => $info): ?>
    <tr>
        <td><?= $symbol ?></td>
        <td><?= $info ?></td>
    </tr>
    <?php endforeach ?>
    </table>
</body>
</html>