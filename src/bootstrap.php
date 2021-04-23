<?php

require \dirname(__DIR__).'/vendor/autoload.php';

try {
    $config = \Kobens\Core\Config::getInstance();
    $config->setConfig(dirname(__DIR__) . '/etc/config.xml');
    $config->setRootDir(dirname(__DIR__));

} catch (\Exception $e) {
    exit("Initialization Error: {$e->getMessage()}");
}

ini_set('memory_limit', '512M');
