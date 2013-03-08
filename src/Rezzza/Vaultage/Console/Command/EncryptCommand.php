<?php

namespace Rezzza\Vaultage\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * EncryptCommand
 *
 * @uses BaseCommand
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class EncryptCommand extends BaseCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('encrypt')
            ->setDescription('Encrypt files');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $vaultage = $this->getVaultage($input->getOption('configuration-file'));
        $metadata = $vaultage->getMetadata();

        if ($metadata->needsPassphrase) {
            $this->askTwoTimesForPassphrase($metadata, $output);
        }

        $files = $this->getAskedFiles($input, $metadata, self::ENCRYPT);

        foreach ($files as $file) {
            try {
                $write   = $input->getOption('write');
                $result  = $vaultage->encrypt($file, $write);
                $message = $write ? 'File <comment>%s</comment> was encrypted.' : 'File <comment>%s</comment> would be encrypted if write option.';

                $output->writeln(sprintf($message, $file->getFrom()));

                if ($input->getOption('verbose')) {
                    $output->writeln(sprintf('Encrypted data: %s', $result));
                }
            } catch (\Exception $e) {
                $output->writeln(sprintf('<error>Cannot encrypt file %s: %s</error>', $file->getFrom(), $e->getMessage()));
            }
        }

        $output->writeln('<info>Done</info>');
    }
}
