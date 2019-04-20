<?php

require \dirname(__DIR__).'/src/bootstrap.php';

use Kobens\Monitor\ResourceReporter as Reporter;

$defaultHours = 1;
$hours = \array_key_exists('hours', $_GET) ? (int) $_GET['hours'] : $defaultHours;
if ($hours < 1) {
    $hours = $defaultHours;
} elseif ($hours > 168) {
    $hours = $defaultHours;
}

\header('content-type:application/json');
echo \json_encode((new Reporter(60 * 60 * $hours, 'cpu_memory.'))->getData());
