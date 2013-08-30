<?php

namespace Rezzza\Vaultage\Console\Command;

use Rezzza\Vaultage\Resource;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->setDescription('Decrypt files')
            ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resource = $this->getBackend($input->getOption('configuration-file'))
            ->setIO($this->getIO())
            ->decrypt(
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
            $output->writeln((string) $resource);
        }
    }
}
