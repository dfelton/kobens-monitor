<?php

require \dirname(__DIR__).'/vendor/autoload.php';

use Kobens\Core\Config;

try {
    new Config(\dirname(__DIR__).'/etc/config.xml', \dirname(__DIR__));
} catch (\Exception $e) {
    exit("Initialization Error: {$e->getMessage()}");
}
