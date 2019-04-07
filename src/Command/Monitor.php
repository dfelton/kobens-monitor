<?php

namespace ProcessMonitor\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};
use Symfony\Component\Console\Output\OutputInterface;
use ProcessMonitor\Watcher;

final class Monitor extends Command
{
    protected function configure()
    {
        $this->setName('monitor');
        $this->setDescription('Monitor process who\'s command matches a given pattern.');
        $this->addArgument('pattern', InputArgument::REQUIRED, 'pattern to match for monitoring');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $watcher = new Watcher($input->getArgument('pattern'));
        $watcher->watch();
    }
}
