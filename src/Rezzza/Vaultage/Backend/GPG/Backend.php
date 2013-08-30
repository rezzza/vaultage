<?php

namespace Rezzza\Vaultage\Backend\GPG;

use Rezzza\Vaultage\Backend\AbstractBackend;
use Rezzza\Vaultage\Backend\BackendInterface;
use Rezzza\Vaultage\File;
use Symfony\Component\Process\Process;

class Backend extends AbstractBackend implements BackendInterface
{
    public function __construct()
    {
        $this->metadata = new Metadata();
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt()
    {
        $files            = $this->getFiles(self::ENCRYPT);
        $defaultArguments = $this->buildDefaultArguments();

        if ($this->metadata->asymmetric) {
            $recipientsString = '';
            foreach ($this->metadata->recipients as $recipient) {
                $recipientsString .= sprintf('-r "%s" ', $recipient);
            }

            $command = sprintf('gpg %s %s --encrypt-files %s', $defaultArguments, $recipientsString, implode(' ', $files));
            $this->exec('Encrypt files', $command);
        } else {
            $passphrase = $this->io->askAndRepeatHidden('Enter passphrase: ', 'Confirm passphrase: ');

            foreach ($files as $file) {
                $command = sprintf('gpg %s --no-use-agent --passphrase "%s" -c %s', $defaultArguments, $passphrase, $file);
                $this->exec(sprintf('Encrypt file: %s', $file), $command, null, false);
            }
        }

        if ($this->getInputOption('write')) {
            $this->io->writeln('<info>Files encrypteds</info>');
        } else {
            $this->io->writeln('<info>Files would be encrypteds if --write option.</info>');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt()
    {
        $files            = $this->getFiles(self::ENCRYPT);
        foreach ($files as $k => $file) {
            $files[$k] .= '.gpg';
        }

        $defaultArguments = $this->buildDefaultArguments();

        if (!$this->metadata->asymmetric) {
            $isOk       = false;

            while (!$isOk) {
                $passphrase = $this->io->askHiddenResponse('Enter passphrase: ');
                $command    = sprintf('gpg %s --no-use-agent %s --passphrase "%s" --decrypt-files %s', $defaultArguments, $passphrase, implode(' ', $files));
                try {
                    $this->exec('Decrypt files', $command, null, false);
                    $isOk       = true;
                } catch (\Exception $e) {
                }
            }
        } else {
            $command = sprintf('gpg %s --decrypt-files %s', $defaultArguments, implode(' ', $files));
            $this->exec('Decrypt files', $command);
        }

        if ($this->getInputOption('write')) {
            $this->io->writeln('<info>Files decrypteds</info>');
        } else {
            $this->io->writeln('<info>Files would be decrypteds if --write option.</info>');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function read($path)
    {
        if (!$this->metadata->asymmetric) {
            while (true) {
                $passphrase = $this->io->askHiddenResponse('Enter passphrase: ');
                $command    = sprintf('gpg --no-verbose --no-use-agent --passphrase "%s" -d %s', $passphrase, $path);
                try {
                    return $this->exec('Decrypt files', $command, null, false);
                } catch (\Exception $e) {
                }
            }
        } else {
            $command    = sprintf('gpg --no-verbose -d < %s', $path);
            return $this->exec('Decrypt file', $command, null, false);
        }
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
            $files = $this->io->ask('files: ');

            if (null === $files) {
                break;
            }

            $this->metadata->addFile(new File($files, null, getcwd()));
        }

        $this->dumpMetadatas($configurationFile);

        $this->io->writeln('<info>Vaultage file created.</info>');
    }

    /**
     * @return string
     */
    protected function buildDefaultArguments()
    {
        $arguments   = array();
        $arguments[] = '--yes';

        $write   = $this->getInputOption('write');

        if (!$write) {
            $arguments[] = '--dry-run';
        }

        if ($this->io->isVerbose()) {
            $arguments[] = '--verbose';
        } else {
            $arguments[] = '--no-verbose';
            $arguments[] = '--quiet';
        }

        return implode(' ', $arguments);
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
