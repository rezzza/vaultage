<?php

namespace Rezzza\Vaultage\Console\Command;

use Rezzza\Vaultage\Metadata;
use Rezzza\Vaultage\Vaultage;
use Rezzza\Vaultage\Resource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
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
            ->addOption('files', 'f', InputOption::VALUE_OPTIONAL, 'Make operation on this file (can define multiple file, separated by commas)')
            ->addOption('write', 'w', InputOption::VALUE_NONE, 'Write on files')
            ;
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

    /**
     * @param InputInterface $input input
     *
     * @return ResourceInterface
     */
    protected function getResource(InputInterface $input)
    {
        $files = $input->getOption('files');

        $metadata = $this->getBackend($input->getOption('configuration-file'))
            ->getMetadata();

        if (null === $files) {
            $resource = $metadata->getFiles();
        } else {
            $files = array_filter(array_map('trim', explode(',', $files)));

            if (empty($files)) {
                throw new \Exception('Vaultage cannot recognize resource, please use STDIN, --all or --files=/foo/bar.');
            }

            $coll = new Resource\FileCollection();

            foreach ($files as $file) {
                $coll->add(
                    new Resource\File($file, $metadata->getEncryptedExtension())
                );
            }

            $resource = $coll;
        }

        if ($resource instanceof Resource\FileCollection && count($resource) === 1) {
            $iterator = $resource->getIterator();

            return $iterator[0];
        }

        return $resource;
    }
}
