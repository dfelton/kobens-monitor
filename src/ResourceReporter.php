<?php

namespace Kobens\Monitor;

use Kobens\Core\Config;

final class ResourceReporter
{
    private $since;
    private $prefix;

    public function __construct(int $since, string $prefix = '')
    {
        $this->since = $since;
    }

    public function getData() : array
    {
        $dir = (new Config())->getLogDir();
        $data = [];
        foreach (\scandir($dir) as $file) {
            if (   $file !== '.'
                && $file != '..'
                && \strpos($file, 'cpu_memory.') === 0
                && [] !== $result = $this->parseFile($dir.'/'.$file)
            ) {
                $log = \explode('.', $file);
                $pid = (int) $log[2];
                $log = $log[1];
                if (!\array_key_exists($log, $data)) {
                    $data[$log] = [];
                }
                $data[$log][$pid] = $result;
            }
        }
        return $data;
    }

    private function parseFile(string $filename) : array
    {
        if (!\is_file($filename) || !\is_readable($filename)) {
            throw new \Exception('Unable to read from requested file.');
        }
        $data = [];
        $now = \time();
        if ($now - \filemtime($filename) < $this->since) {
            // last 8 hours if log has 1 line per second.
            // If more than one data point per second we'll lose data
            // (and are probably logging too much for CPU/Memory needs)
            $lines = \shell_exec("tail -n $this->since $filename");
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
