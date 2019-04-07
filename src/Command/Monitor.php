<?php

namespace Kobens\Monitor\Command;

use Kobens\Monitor\Watcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};
use Symfony\Component\Console\Output\OutputInterface;

final class Monitor extends Command
{
    protected function configure()
    {
        $this->setName('monitor');
        $this->setDescription('Monitor process who\'s command matches a given pattern.');
        $this->addArgument('pattern', InputArgument::REQUIRED, 'pattern to match for monitoring');
        $this->addArgument('type', InputArgument::REQUIRED, 'Type of monitoring (cpu|memory)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        if ($type !== 'cpu' && $type !== 'memory') {
            $output->writeln('Invalid monitoring type');
        } else {
            $watcher = new Watcher($input->getArgument('pattern'), $type);
            $watcher->watch();
        }
    }
}
