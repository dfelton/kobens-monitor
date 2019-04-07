<?php

namespace Kobens\Monitor;

use Kobens\Core\Config;

final class Watcher
{
    /**
     * @var string
     */
    private $pattern;

    private $type;

    /**
     * @var Config
     */
    private $logDir;

    public function __construct(string $pattern, string $type)
    {
        if ($pattern === '') {
            throw new \InvalidArgumentException('Pattern cannot be an empty string');
        }
        if ($type !== 'cpu' && $type !== 'memory') {
            throw new \InvalidArgumentException('Watcher only supports "cpu" and "memory" watching');
        }
        $this->pattern = $pattern;
        $this->type = $type;
        $config = new Config();

        $dir = $config->getLogDir();
        if (!\is_dir($dir) || !\is_writable($dir)) {
            throw new \Exception('Log directory unavailable.');
        }
        $this->logDir = $dir;
    }

    public function watch() : void
    {
        do {
            $pid = $this->getPID();
            if ($pid !== 0) {
                $filename = $this->getLogFilename($pid);
                $handle = \fopen($filename, 'a');
                foreach ($this->getUsage($pid) as $data) {
                    \fwrite($handle, "{$data[0]},{$data[1]}\n");
                }
                \fclose($handle);
            }
            \sleep(1);
        } while (true);
    }

    private function getLogFilename(int $pid) : string
    {
        $command =  \str_replace(':', '_', $this->pattern);
        return \sprintf('%s/%s.%s.%s.log', $this->logDir, $this->type, $command, $pid);
    }

    private function getUsage(int $pid) : \Generator
    {
        if ($pid <= 0) {
            throw new \LogicException('Process ID must be greater than zero');
        }
        $isDead = false;
        $column = $this->type === 'cpu' ? '%cpu' : 'rss';
        do {
            $max = 0;
            $time = \time();
            do {
                $current = \rtrim(\shell_exec('ps ax -o '.$column.' '.$pid.' 2>/dev/null | sed -e "1,1d" | awk \'{$1=$1};1\''), PHP_EOL);
                if ($current === '') {
                    $isDead = true;
                } else {
                    $current = $this->type === 'cpu' ? (float) $current : (int) $current;
                    if ($current > $max) {
                        $max = $current;
                    }
                }
                \usleep(0050000); // max 20 polls per second
            } while (!$isDead && $time === \time());

            yield [
                (int)(\microtime(true)*1000),
                $this->type === 'cpu' ? $max : \round($max/1024, 2)
            ];
        } while (!$isDead);
    }

    private function getPID() : int
    {
        $pid = 0;
        $processes = \shell_exec('ps  -o pid,command | sed -e "1,1d"');
        $processes = \explode(PHP_EOL, $processes);
        foreach ($processes as $process) {
            if (\strpos($process, 'kobens-monitor') === false && \strpos($process, $this->pattern) !== false) {
                if ($pid === 0) {
                    $pid = (int) \substr($process, 0, \strpos($process, ' '));
                } else {
                    throw new \Exception('Multiple processes with same pattern detected.');
                }
            }
        }
        return $pid;
    }
}