<?php

namespace Rezzza\Vaultage\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Rezzza\Vaultage\File;
use Rezzza\Vaultage\Vaultage;
use Rezzza\Vaultage\Backend\Factory;

/**
 * InitializeCommand
 *
 * @uses Command
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class InitializeCommand extends BaseCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Initialize a new .vaultage.json file')
            ->addOption('configuration-file', 'c', InputOption::VALUE_OPTIONAL, 'Custom configuration file')
            ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = getcwd();
        $config    = $input->getOption('configuration-file') ?: $directory.DIRECTORY_SEPARATOR.'.vaultage.json';

        if (file_exists($config)) {
            throw new \InvalidArgumentException(sprintf('Configuration file "%s" is already exists.', $config));
        }

        $io       = $this->getIO();
        $backends = Factory::getAvailableBackends();

        $validation = function ($v) use ($backends) {
            if (!in_array($v, array_values($backends))) {
                throw new \InvalidArgumentException(sprintf('Backend "%s" is invalid.', $v));
            }

            return $v;
        };

        $default = 'basic';
        $backend = $io->askAndValidate(
            sprintf('Choose a backend from <comment>%s</comment> (default is %s): ', implode(', ', $backends), $default), 
            $validation, false, $default, $backends
        );

        Factory::create($backend)
            ->setIO($io)
            ->initialize($config);
    }
}
