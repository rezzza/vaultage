<?php

namespace Rezzza\Vaultage\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * DecryptCommand
 *
 * @uses BaseCommand
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class DecryptCommand extends BaseCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('decrypt')
            ->setDescription('Decrypt files');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $vaultage = $this->getVaultage($input->getOption('configuration-file'));
        $metadata = $vaultage->getMetadata();

        if ($metadata->needsPassphrase) {
            $this->askForPassphrase($metadata, $output);
        }

        $files = $this->getAskedFiles($input, $metadata, self::DECRYPT);

        foreach ($files as $file) {
            try {
                $write   = $input->getOption('write');
                $result  = $vaultage->decrypt($file, $write);
                $message = $write ? 'File <comment>%s</comment> was decrypted.' : 'File <comment>%s</comment> would be decrypted if write option.';

                $output->writeln(sprintf($message, $file->getTo()));

                if ($input->getOption('verbose')) {
                    $output->writeln(sprintf('Decrypted data: %s', $result));
                }
            } catch (\Exception $e) {
                $output->writeln(sprintf('<error>Cannot decrypt file %s: %s</error>', $file->getTo(), $e->getMessage()));
            }
        }

        $output->writeln('<info>Done</info>');
    }
}
