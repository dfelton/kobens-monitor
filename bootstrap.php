<?php

require __DIR__.'/vendor/autoload.php';

use Kobens\Core\Config;

try {
    new Config(__DIR__.'/etc/config.xml', __DIR__);
} catch (\Exception $e) {
    exit("Initialization Error: {$e->getMessage()}");
}