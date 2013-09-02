<?php

namespace Rezzza\Vaultage\Backend\GPG;

use Rezzza\Vaultage\Backend\AbstractBackend;
use Rezzza\Vaultage\Backend\BackendInterface;
use Rezzza\Vaultage\Resource;
use Symfony\Component\Process\Process;
use Rezzza\Vaultage\Resource\ResourceInterface;

/**
 * Backend
 *
 * @uses AbstractBackend
 * @uses BackendInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Backend extends AbstractBackend implements BackendInterface
{
    public function __construct()
    {
        $this->metadata = new Metadata();

        $this->addResourceProcessor(new ResourceProcessor\File());
        $this->addResourceProcessor(new ResourceProcessor\FileCollection());
    }

    /**
     * {@inheritdoc}
     */
    public function initialize($configurationFile)
    {
        $this->exec('Looking for GPG on this system', 'which gpg', '<error>GPG is not installed on this system.</error>');

        $asymmetric = $this->io->askConfirmation('Asymmetric cryptage <comment>(Y/n)</comment>: ');
        $this->metadata->asymmetric = $asymmetric;

        if ($this->metadata->asymmetric) {
            $output       = $this->exec('Extract recipients', 'gpg --list-keys | grep uid | sed s/uid//');
            $autocomplete = array_map('trim', explode(chr(10), $output));

            $end        = false;
            $recipients = array();

            while (!$end) {
                $recipient = trim($this->io->ask('<comment>Add a recipient:</comment> (press return to stop adding recipients) ', null, $autocomplete));
                if ('' === $recipient) {
                    if (count($recipients) === 0) {
                        $this->io->writeln('<error>You have to enter at least ONE recipient.</error>');
                    } else {
                        $end = true;
                    }
                } else {
                    $recipients[] = $recipient;
                }
            }

            $this->metadata->recipients = $recipients;
        }

        $this->io->writeln('<comment>Enter couple of files you wanna vault : "path/to/encrypted_file" (press return to stop adding files)</comment>');

        while (true) {
            $file = $this->io->ask('files: ');

            if (null === $file) {
                break;
            }

            $this->metadata->getFiles()->add(new Resource\File($file, $this->metadata->getEncryptedExtension()));
        }

        $this->dumpMetadatas($configurationFile);

        $this->io->writeln('<info>Vaultage file created.</info>');
    }

    protected function exec($label, $command, $exitMessage = null, $showCommand = true)
    {
        $verbose = $this->io->isVerbose();
        if ($verbose && $showCommand) {
            $this->io->write(sprintf('<info>➜</info> <comment>%s</comment>: %s ', $label, $command));
        }

        $process = new Process($command);
        $process->setTimeout(3600);
        $process->run();

        if (!$process->isSuccessful()) {

            $this->io->writeln("\n".sprintf('<error>Fail: %s</error>', $process->getErrorOutput()));
            $exitMessage = $exitMessage ?: $process->getErrorOutput();
            throw new \Exception($exitMessage);
        }

        if ($verbose) {
            $this->io->writeln('<info>✔</info>');
        }

        return trim($process->getOutput());
    }
}
