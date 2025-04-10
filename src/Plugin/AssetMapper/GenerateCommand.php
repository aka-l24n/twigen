<?php

namespace l24n\Twigen\Plugin\AssetMapper;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'asset:generate')]
class GenerateCommand extends Command
{
    protected function configure(): void
    {
        $this->setDescription('Generate assets to the public directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return Command::SUCCESS;
    }
}