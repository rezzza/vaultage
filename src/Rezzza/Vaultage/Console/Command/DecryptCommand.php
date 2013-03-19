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
        $this->getBackend($input->getOption('configuration-file'))
            ->setIO($this->getIO())
            ->decrypt();
    }
}
