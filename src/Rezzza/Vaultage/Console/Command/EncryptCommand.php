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
        $resource = $this->getBackend($input->getOption('configuration-file'))
            ->setIO($this->getIO())
            ->encrypt(
                $this->getResource($input),
                array(
                    'write' => $input->getOption('write'),
                    'verbose' => $input->getOption('verbose'),
                )
            );

        if ($input->getOption('write')) {
            $output->writeln('<info>Writing ...</info>');
            $resource->write();
        } else {
            $output->writeln('<info>DRY RUN mode... Add --write to deactivate this mode.</info>');
        }

        if ($input->getOption('verbose')) {
            foreach ($resource as $file) {
                $output->writeln(sprintf('<comment>============================= %s =============================</comment>', $file->getSourceFile()));
                $output->writeln($file->getTargetContent());
            }
        }
    }
}
