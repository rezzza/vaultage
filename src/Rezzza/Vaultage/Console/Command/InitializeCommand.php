<?php

namespace Rezzza\Vaultage\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Rezzza\Vaultage\Compiler\Compiler;
use Rezzza\Vaultage\File;
use Rezzza\Vaultage\Metadata;
use Rezzza\Vaultage\Vaultage;

/**
 * InitializeCommand
 *
 * @uses Command
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class InitializeCommand extends Command
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

        $vaultage  = new Vaultage($config);
        $metadata  = $vaultage->getMetadata();

        $dialog     = $this->getHelperSet()->get('dialog');
        $key        = $dialog->ask($output, 'Enter key <comment>(if it is located in file, type file://....): </comment>');

        if (strpos($key, 'file://') === 0) {
            $metadata->keyFile = $key;
            // here we could generate him a key
        } else {
            $metadata->key     = $key;
        }

        $metadata->needsPassphrase = $dialog->askConfirmation($output, 'Using passphrase <comment>(y/n)</comment>: ');

        while(true) {
            $files = $dialog->ask($output, 'Enter files used by vaultage: <comment>"path/to/decrypted_file,path/to/encrypted_file"</comment>, press <return> to stop adding files: ');

            if (null === $files) {
                break;
            }

            $files = explode(',', $files);
            if (count($files) != 2) {
                $output->writeln('<error>Please, respect format "path/to/decrypted_file,path/to/encrypted_file"</error>');
                continue;
            }

            $metadata->addFile(new File($files[0], $files[1], $directory));
        }

        $vaultage->dumpMetadatas();

        $output->writeln('<info>Vaultage file created.</info>');
    }
}
