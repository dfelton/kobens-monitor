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
    foreach ((new Orders())->getOrders($pair->getSymbol(), 'BUY_PLACED') as $order) {
        if (($data[$pair->getSymbol()] ?? null) === null) {
            $data[$pair->getSymbol()] = [
                'total_quote' => '0',
                'total_orders' => 0,
            ];
        }
        $data[$pair->getSymbol()]['total_quote'] = Add::getResult(
            $data[$pair->getSymbol()]['total_quote'],
            Multiply::getResult(
                $order['buy_amount'],
                $order['buy_price']
            )
        );
    }
}

ksort($data);
?>
<html>
<head>
<link rel="stylesheet" href="css/styles.css"/>
<style type="text/css">
tr:hover{background:#808080}
</style>
</head>
<body>
    <h2>Buy Order Quote Totals</h2>
    <table class="data-table">
    <?php foreach ($data as $symbol => $info): ?>
    <tr>
        <td><?= $symbol ?></td>
        <td><?= $info['total_quote'] ?></td>
    </tr>
    <?php endforeach ?>
    </table>
</body>
</html>