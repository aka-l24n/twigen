<?php

namespace l24n\Twigen\Plugin\ServerRender;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'app:serve')]
class ServeCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setDescription('Run the built-in PHP web server.')
            ->addOption(
                'host',
                null,
                InputOption::VALUE_OPTIONAL,
                'The host address where the server will run',
                '127.0.0.1'
            )
            ->addOption(
                'port',
                null,
                InputOption::VALUE_OPTIONAL,
                'The port where the server will run',
                8000
            )
            ->addOption(
                'dir',
                null,
                InputOption::VALUE_OPTIONAL,
                'The directory to serve',
                './demo/public'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $host = $input->getOption('host');
        $port = $input->getOption('port');
        $directory = $input->getOption('dir');

        if (!is_dir($directory)) {
            $output->writeln(sprintf('<error>The directory "%s" does not exist.</error>', $directory));
            return Command::FAILURE;
        }

        $address = sprintf('%s:%d', $host, $port);
        $output->writeln(sprintf('Starting PHP built-in server at <info>http://%s</info>', $address));
        $output->writeln(sprintf('Serving files from <info>%s</info>', realpath($directory)));

        // Ensure the directory has an index.php or fallback to a routing file
        $indexFile = realpath($directory . '/index.php') ?: $directory . '/router.php';
        if (!file_exists($indexFile)) {
            $output->writeln('<error>No index.php or router.php found in the specified directory.</error>');
            return Command::FAILURE;
        }

        // Run the built-in PHP server
        $command = sprintf(
            'php -S %s -t %s',
            escapeshellarg($address),
            escapeshellarg($directory)
        );

        $output->writeln('<info>Press Ctrl+C to stop the server</info>');

        // Execute the PHP built-in server
        passthru($command);

        return Command::SUCCESS;
    }
}