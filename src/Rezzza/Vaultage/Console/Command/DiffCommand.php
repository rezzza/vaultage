<?php

namespace Rezzza\Vaultage\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Rezzza\Vaultage\Compiler\Compiler;
use SebastianBergmann\Diff\Differ;

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
            ->addOption('crypted', 'e', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY)
            ->addOption('decrypted', 'd', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY)
            ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cryptedFiles   = $input->getOption('crypted');
        $decryptedFiles = $input->getOption('decrypted');

        if (count($cryptedFiles) + count($decryptedFiles) !== 2) {
            throw new \InvalidArgumentException('You have to define 2 files.');
        }

        $files = array();

        $backend = $this->getBackend($input->getOption('configuration-file'))
            ->setIO($this->getIO());

        foreach ($decryptedFiles as $decryptedFile) {
            if (!is_readable($decryptedFile)) {
                throw new \LogicException(sprintf('File "%s" is not readable.', $decryptedFile));
            }

            $files[] = array(
                'file' => $decryptedFile,
                'content'   => rtrim(file_get_contents($decryptedFile))
            );
        }

        foreach ($cryptedFiles as $cryptedFile) {
            $files[] = array(
                'file' => $cryptedFile,
                'content' => $backend->read($cryptedFile)
            );
        }

        $output->writeln(sprintf('<info>Diff between <comment>%s</comment> and <comment>%s</comment></info>', $files[0]['file'], $files[1]['file']));

        $from   = $files[0]['content'];
        $to     = $files[1]['content'];

        if ($from == $to) {
            $output->writeln('no diff.');
        } else {
            $differ = new Differ();
            echo $differ->diff($from, $to);
        }
    }
}
