<?php

namespace Rezzza\Vaultage\Console\Command;

use Rezzza\Vaultage\Metadata;
use Rezzza\Vaultage\Vaultage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * BaseCommand
 *
 * @uses Command
 * @author Stephane PY <py.stephane1@gmail.com>
 */
abstract class BaseCommand extends Command
{
    CONST ENCRYPT = 'encrypt'; 
    CONST DECRYPT = 'decrypt'; 

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
    protected function getVaultage($config)
    {
        if (null === $config) {
            $config = getcwd().DIRECTORY_SEPARATOR.'.vaultage.json';
        }

        return new Vaultage($config);
    }

    /**
     * @param Metadata        $metadata metadata
     * @param OutputInterface $output   output
     */
    protected function askTwoTimesForPassphrase(Metadata $metadata, OutputInterface $output)
    {
        $isOk   = false;
        $dialog = $this->getHelperSet()->get('dialog');

        while (!$isOk) {
            $passphrase       = $dialog->askHiddenResponse($output, 'Enter passphrase: ');
            $secondPassphrase = $dialog->askHiddenResponse($output, 'Confirm passphrase: ');

            if ($secondPassphrase == $passphrase) {
                $metadata->passphrase = $passphrase;
                $isOk = true;
            } else {
                $output->writeln('<error>Two typed passphrases are not identical, retry:</error>');
            }
        }
    }

    /**
     * @param Metadata        $metadata metadata
     * @param OutputInterface $output   output
     */
    protected function askForPassphrase(Metadata $metadata, OutputInterface $output)
    {
        $dialog               = $this->getHelperSet()->get('dialog');
        $metadata->passphrase = $dialog->askHiddenResponse($output, 'Enter passphrase: ');
    }

    /**
     * @param InputInterface $input    input
     * @param Metadata       $metadata metadata
     * @param string         $type     type
     * 
     * @return array<File>
     */
    protected function getAskedFiles(InputInterface $input, Metadata $metadata, $type)
    {
        $files = $input->getOption('files');

        if (empty($files)) {
            return $metadata->getFiles();
        }

        $files  = array_filter(explode(',', $files));
        $data   = array();
        $method = $type === self::ENCRYPT ? 'findDecryptedFile' : 'findCryptedFile';

        foreach ($files as $file) {
            $data[] = $metadata->{$method}($file);
        }

        return array_filter($data);
    }
}
