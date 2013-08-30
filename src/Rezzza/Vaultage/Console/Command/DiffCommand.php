<?php

namespace Rezzza\Vaultage\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Rezzza\Vaultage\Compiler\Compiler;
use SebastianBergmann\Diff\Differ;
use Rezzza\Vaultage\Resource;

/**
 * DiffCommand
 *
 * @uses Command
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class DiffCommand extends BaseCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('diff')
            ->setDescription('Show diff between crypted file with crypted file|file.')
            ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = $this->getResource($input);
        if (count($files) != 2) {
            throw new \InvalidArgumentException('You have to define 2 files.');
        }

        $results   = array();

        foreach ($files as $file) {
            if ($file->isCrypted()) {
                $file = $this->getBackend($input->getOption('configuration-file'))
                    ->setIO($this->getIO())
                    ->decrypt($file)
                    ;
            }
        }

        $it = $files->getIterator();
        $output->writeln(
            sprintf('<info>Diff between <comment>%s</comment> and <comment>%s</comment></info>', $it[0]->getSourceFile(), $it[1]->getSourceFile())
        );

        $from   = $this->clean($it[0]->isCrypted() ? $it[0]->getTargetContent() : $it[0]->getSourceContent());
        $to     = $this->clean($it[1]->isCrypted() ? $it[1]->getTargetContent() : $it[1]->getSourceContent());

        if ($from == $to) {
            $output->writeln('no diff.');
        } else {
            $differ = new Differ();
            echo $differ->diff($from, $to);
        }
    }

    protected function clean($v)
    {
        return rtrim($v);
    }
}
