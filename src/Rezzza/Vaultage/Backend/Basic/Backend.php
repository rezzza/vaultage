<?php

namespace Rezzza\Vaultage\Backend\Basic;

use Rezzza\Vaultage\Resource;
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
    /**
     * constructor
     */
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
        $this->metadata->setEncryptedExtension('crypted');

        $this->io->writeln('<comment>Enter files you wanna vault : "path/to/decrypted_file" (press return to stop adding files)</comment>');

        while (true) {
            $file = $this->io->ask('file: ');

            if (null === $file) {
                break;
            }

            $this->metadata->getFiles()->add(new Resource\File($file, $this->metadata->getEncryptedExtension()));
        }

        $this->dumpMetadatas($configurationFile);

        $this->io->writeln('<info>Vaultage file created.</info>');
    }
}
