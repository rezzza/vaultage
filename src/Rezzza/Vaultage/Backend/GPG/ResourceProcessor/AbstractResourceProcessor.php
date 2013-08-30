<?php

namespace Rezzza\Vaultage\Backend\GPG\ResourceProcessor;

use Symfony\Component\Process\Process;

/**
 * AbstractResourceProcessor
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
abstract class AbstractResourceProcessor
{
    /**
     * @return string
     */
    protected function getDefaultArguments($write = false, $verbose = false)
    {
        $arguments   = array();
        $arguments[] = '--yes';

        if (!$write) {
            $arguments[] = '--dry-run';
        }

        if ($verbose) {
            $arguments[] = '--verbose';
        } else {
            $arguments[] = '--no-verbose';
            $arguments[] = '--quiet';
        }

        return implode(' ', $arguments);
    }

    protected function exec($io, $label, $command, $exitMessage = null, $showCommand = true)
    {
        $verbose = $io->isVerbose();

        if ($verbose && $showCommand) {
            $io->write(sprintf('<info>➜</info> <comment>%s</comment>: %s ', $label, $command));
        }

        $process = new Process($command);
        $process->setTimeout(3600);
        $process->run();

        if (!$process->isSuccessful()) {

            $io->writeln("\n".sprintf('<error>Fail: %s</error>', $process->getErrorOutput()));
            $exitMessage = $exitMessage ?: $process->getErrorOutput();
            throw new \Exception($exitMessage);
        }

        if ($verbose) {
            $io->writeln('<info>✔</info>');
        }

        return trim($process->getOutput());
    }
}
