<?php

namespace Rezzza\Vaultage\Backend\Basic;

use Rezzza\Vaultage\File;
use Rezzza\Vaultage\Backend\AbstractBackend;
use Rezzza\Vaultage\Backend\BackendInterface;
use Rezzza\Vaultage\Exception\ResourceException;

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
        $this->cipher   = new Cipher();
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt()
    {
        $files   = $this->getFiles(self::ENCRYPT);
        $write   = $this->getInputOption('write');
        $verbose = $this->getInputOption('verbose');

        if ($this->metadata->needsPassphrase) {
            $this->metadata->passphrase = $this->io->askAndRepeatHidden('Enter passphrase: ', 'Confirm passphrase: ');
        }

        foreach ($files as $file) {
            $from = new \SplFileInfo($file->getFrom(File::ABSOLUTE_PATH));

            if (!$from->isReadable()) {
                throw new ResourceException(sprintf('File "%s" is not readable', $from));
            }

            $data = $this->cipher
                ->encrypt(file_get_contents($from), $this->metadata);

            if ($write) {
                $to = new \SplFileInfo($file->getTo(File::ABSOLUTE_PATH));

                $to->openFile('w+')
                    ->fwrite($data);
            }

            $message = $write ? 'File <comment>%s</comment> was encrypted.' : 'File <comment>%s</comment> would be encrypted if write option.';
            $this->io->writeln(sprintf($message, $file->getFrom()));

            if ($verbose) {
                $this->io->writeln(sprintf('Encrypted data: %s', $data));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt()
    {
        $files   = $this->getFiles(self::DECRYPT);
        $write   = $this->getInputOption('write');
        $verbose = $this->getInputOption('verbose');

        $processed = false;

        while (!$processed) {

            if ($this->metadata->needsPassphrase) {
                $this->metadata->passphrase = $this->io->askHiddenResponse('Enter passphrase: ');
            } else {
                // there is no way of misstyping, retry will make a infinite loop.
                $processed = true;
            }

            foreach ($files as $file) {
                try {
                    $to   = new \SplFileInfo($file->getTo(File::ABSOLUTE_PATH));

                    if (!$to->isReadable()) {
                        throw new ResourceException(sprintf('File "%s" is not writable', $to));
                    }

                    $data = $this->cipher
                        ->decrypt(file_get_contents($to), $this->metadata);

                    if ($write) {
                        $from = new \SplFileInfo($file->getFrom(File::ABSOLUTE_PATH));

                        $from->openFile('w+')
                            ->fwrite($data);
                    }

                    $message = $write ? 'File <comment>%s</comment> was decrypted.' : 'File <comment>%s</comment> would be decrypted if write option.';

                    $this->io->writeln(sprintf($message, $file->getTo()));

                    if ($verbose) {
                        $this->io->writeln(sprintf('Decrypted data: %s', $data));
                    }
                    $processed = true;
                } catch (\Exception $e) {
                    $this->io->writeln(sprintf('<error>Cannot decrypt file %s: %s</error>', $file->getTo(), $e->getMessage()));
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function initialize($configurationFile)
    {
        $key = $this->io->ask('Enter key <comment>(if it is located in file, type file://....): </comment>');

        if (strpos($key, 'file://') === 0) {
            $this->metadata->keyFile = $key;
            $keyFile = $this->metadata->getAbsoluteKeyFile();
            // here we could generate him a key
            if (!file_exists($keyFile)) {
                if ($this->io->askConfirmation('Do you want we generate a key for you? <comment>(Y/n)</comment>: ')) {
                    file_put_contents($keyFile, hash('sha512', uniqid()));
                }
            }
        } else {
            $this->metadata->key     = $key;
        }

        $this->metadata->needsPassphrase = $this->io->askConfirmation('Using passphrase <comment>(Y/n)</comment>: ');

        $this->io->writeln('<comment>Enter coma separated couple of files you wanna vault : "path/to/decrypted_file,path/to/encrypted_file" (press return to stop adding files)</comment>');

        while (true) {
            $files = $this->io->ask('files: ');

            if (null === $files) {
                break;
            }

            $files = explode(',', $files);
            if (count($files) != 2) {
                $this->io->writeln('<error>Please, respect format "path/to/decrypted_file,path/to/encrypted_file"</error>');
                continue;
            }

            $this->metadata->addFile(new File($files[0], $files[1], getcwd()));
        }

        $this->dumpMetadatas($configurationFile);

        $this->io->writeln('<info>Vaultage file created.</info>');
    }
}
