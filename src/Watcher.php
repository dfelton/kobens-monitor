<?php

namespace Kobens\Monitor;

use Kobens\Core\Config;

final class Watcher
{
    /**
     * @var string
     */
    private $pattern;

    /**
     * @var Config
     */
    private $logDir;

    public function __construct(string $pattern)
    {
        if ($pattern === '') {
            throw new \InvalidArgumentException('Pattern cannot be an empty string');
        }
        $this->pattern = $pattern;
        $dir = (new Config())->getLogDir();
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
                    \fwrite($handle, "{$data[0]},{$data[1]},{$data[2]}\n");
                }
                \fclose($handle);
            }
            \sleep(1);
        } while (true);
    }

    private function getLogFilename(int $pid) : string
    {
        $command =  \str_replace(':', '_', $this->pattern);
        return \sprintf('%s/cpu_memory.%s.%s.log', $this->logDir, $command, $pid);
    }

    private function getUsage(int $pid) : \Generator
    {
        if ($pid <= 0) {
            throw new \LogicException('Process ID must be greater than zero');
        }
        $isDead = false;
        do {
            $maxMemory = 0;
            $maxCpu = 0;
            $time = \time();
            do {
                $current = \trim(\shell_exec('ps ax -o %cpu,rss '.$pid.' 2>/dev/null | sed -e "1,1d" | sed "s/  */ /g"'));
                if ($current === '') {
                    $isDead = true;
                } else {
                    $current = explode(' ', $current);
                    $current[0] = (float) $current[0];
                    $current[1] = (int) $current[1];
                    if ($current[0] > $maxCpu) {
                        $maxCpu = $current[0];
                    }
                    if ($current[1] > $maxMemory) {
                        $maxMemory = $current[1];
                    }
                }
                \usleep(0050000); // max 20 polls per second
            } while (!$isDead && $time === \time());

            yield [
                (int)(\microtime(true)*1000),
                $maxCpu,
                $maxMemory,
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