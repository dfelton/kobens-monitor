<?php

namespace Kobens\Monitor;

final class ResourceReporter
{
    private $since;

    public function __construct(int $since)
    {
        $this->since = $since;
    }

    public function getData(string $filename)
    {
        if (!\is_file($filename) || !\is_readable($filename)) {
            throw new \Exception('Unable to read from requested file.');
        }
        $data = [];
        $now = \time();
        if ($now - \filemtime($filename) < $this->since) {
            $lines = \shell_exec("tail -n $this->since $filename"); // last 8 hours if log has 1 line per second
            $lines = \explode(PHP_EOL, \trim($lines, PHP_EOL));
            foreach ($lines as $line) {
                $line = \explode(',', $line);
                $line[0] = (int) $line[0];
                $line[1] = (float) $line[1];
                $line[2] = (int) $line[2];
                $age = $now - \substr($line[0], 0, -3);
                if ($age < $this->since) {
                    $data[] = [$line[0], $line[1], $line[2]];
                }
            }
        }
        return $data;
    }
}