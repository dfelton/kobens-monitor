<?php

require \dirname(__DIR__).'/bootstrap.php';

use Kobens\Core\Config;
use Kobens\Monitor\ResourceReporter;

$defaultHours = 8;
$hours = \array_key_exists('hours', $_GET) ? (int) $_GET['hours'] : $defaultHours;
if ($hours < 1) {
    $hours = $defaultHours;
} elseif ($hours > 168) {
    $hours = $defaultHours;
}

$config = new Config();
$reporter = new ResourceReporter(60 * 60 * $hours);
$dir = $config->getLogDir();
$data = [];

foreach (\scandir($dir) as $file) {
    if ($file !== '.' && $file != '..' && \strpos($file, 'cpu.') === 0) {
        $filename = $dir.'/'.$file;
        $result = $reporter->getData($filename);
        if ($result) {
            $log = \explode('.', $file);
            $pid = (int) $log[2];
            $log = $log[1];
            if (!\array_key_exists($log, $data)) {
                $data[$log] = [];
            }
            $data[$log][$pid] = $result;
        }
    }
}

\header('content-type:application/json');
echo \json_encode($data);