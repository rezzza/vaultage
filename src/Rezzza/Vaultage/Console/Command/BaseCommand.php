<?php

namespace Rezzza\Vaultage\Console\Command;

use Rezzza\Vaultage\Metadata;
use Rezzza\Vaultage\Vaultage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Rezzza\Vaultage\Console\Application;
use Rezzza\Vaultage\IO\IOInterface;

/**
 * BaseCommand
 *
 * @uses Command
 * @author Stephane PY <py.stephane1@gmail.com>
 */
abstract class BaseCommand extends Command
{
    protected $io;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->addOption('configuration-file', 'c', InputOption::VALUE_OPTIONAL, 'Custom configuration file')
            ->addOption('write', 'w', InputOption::VALUE_NONE, 'Write on files')
            ->addOption('files', 'f', InputOption::VALUE_OPTIONAL, 'Make operation on this file (can define multiple file, separated by commas)');
    }

    /**
     * @param string|null $config config
     *
     * @return Vaultage
     */
    protected function getBackend($config)
    {
        if (null === $config) {
            $config = getcwd().DIRECTORY_SEPARATOR.'.vaultage.json';
        }

        $vaultage = new Vaultage();

        return $vaultage->buildBackend($config);
    }

    /**
     * @return IOInterface
     */
    public function getIo()
    {
        if (null === $this->io) {
            $application = $this->getApplication();
            if ($application instanceof Application) {
                /* @var $application    Application */
                $this->io = $application->getIO();
            } else {
                throw new \LogicException('Works on console environment.');
            }
        }

        return $this->io;
    }
}
