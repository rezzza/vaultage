<?php

namespace Rezzza\Vaultage\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Rezzza\Vaultage\Compiler\Compiler;

/**
 * CompileCommand
 *
 * @uses Command
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class CompileCommand extends Command
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('compile')
            ->setDescription('Compile as a phar file.');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $compiler = new Compiler();
        $compiler->compile();
    }
}
