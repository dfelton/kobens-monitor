<?php

use Kobens\Gemini\Exchange\Currency\Pair;

require __DIR__ . '/vendor/autoload.php';

?><!DOCTYPE HTML>
<html>
<head>
    <title>xkcd.com/1732/</title>
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="css/styles.css"/>
</head>
<body>
    <div class="page">
        <p><a href="/" class="home">Home</a></p>

        <select id="symbol" onchange="gemini.update()" tabindex="1">
            <?php foreach (Pair::getAllInstances() as $pair): ?>
                <?php if ($pair->getQuote()->getSymbol() === 'usd'): ?>
                    <option value="<?= $pair->getSymbol() ?>"><?= $pair->getBase()->getSymbol() ?></option>
                <?php endif ?>
            <?php endforeach ?>
        </select>

        <button id="update" tabindex="2" onclick="window.gemini.update()">Update</button>
        <!--
            FIXME: Need to have this application's calls obey api request limits.
            For now, turning this on can cause problems with the kobens-gemini bot activity
            and cause that application to shut down operations entirely.
            <input type="checkbox" id="auto-update" tabindex="3" /><label for="auto-update">Auto Update</label>
        -->

        <table class="data-table">
            <tr>
                <td>Total Orders: <span id="total-orders">0</span></td>
                <td>Total Buy Orders: <span id="total-buy-orders">0</span></td>
            </tr>
            <tr>
                <td>Total USD Held in Buy Orders: <span id="total-buy-usd">0</span></td>
                <td>Total Sell Orders: <span id="total-sell-orders">0</span></td>
            </tr>
            <tr>
                <td>Total USD Held in Sell Orders: <span id="total-sell-usd">0</span></td>
                <td>Total <span id="total-base-label"></span> Purchased: <span id="total-base-purchased">0</span></td>
            </tr>
            <tr>
                <td>Average price of purchased <span id="avg-base-label"></span>: <span id="avg-base-price">0</span></td>
                <td>Current Value: <span id="current-value">0</span></td>
            </tr>
        </table>

        <div class="container-fluid monitors clear-after" id="orders">
            <div class="monitor orders">
                <div class="monitor-box clear-after" id="canvas_chart"></div>
            </div>
        </div>

    </div>
    <script src="js/assets/jquery-3.3.1.min.js"></script>
    <script src="js/assets/canvasjs.min.js"></script>
    <script src="js/monitor-orders.js"></script>
</body>
</html>
