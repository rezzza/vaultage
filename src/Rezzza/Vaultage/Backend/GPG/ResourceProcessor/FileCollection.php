<?php

namespace Rezzza\Vaultage\Backend\GPG\ResourceProcessor;

use Rezzza\Vaultage\Backend\BackendInterface;
use Rezzza\Vaultage\Backend\ResourceProcessorInterface;
use Rezzza\Vaultage\Resource\ResourceInterface;
use Rezzza\Vaultage\Resource\FileCollection as Resource;

class FileCollection extends AbstractResourceProcessor implements ResourceProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function accepts(ResourceInterface $resource)
    {
        return $resource instanceof Resource;
    }

    public function encrypt(ResourceInterface $resource, BackendInterface $backend, array $options = array())
    {
        $io               = $backend->getIo();
        $metadata         = $backend->getMetadata();
        $defaultArguments = $this->getDefaultArguments(isset($options['write']) ? $options['write'] : false, $io->isVerbose());

        if ($metadata->asymmetric) {
            $recipientsString = '';
            foreach ($metadata->recipients as $recipient) {
                $recipientsString .= sprintf('-r "%s" ', $recipient);
            }

            $files   = $resource->getDecryptedPaths();
            $command = sprintf('gpg %s %s --encrypt-files %s', $defaultArguments, $recipientsString, implode(' ', $files));
            $this->exec($io, 'Encrypt files', $command);
        } else {
            $passphrase = $io->askAndRepeatHidden('Enter passphrase: ', 'Confirm passphrase: ');
            $files      = $resource->getDecryptedPaths();

            foreach ($files as $file) {
                $command = sprintf('gpg %s --no-use-agent --passphrase "%s" -c %s', $defaultArguments, $passphrase, $file);
                $this->exec($io, sprintf('Encrypt file: %s', $file), $command, null, false);
            }
        }

        if ($options['write']) {
            $resource->readTargetContent();
        }

        return $resource;

    }

    public function decrypt(ResourceInterface $resource, BackendInterface $backend, array $options = array())
    {
        foreach ($resource as $file) {
            if (!$file->isCrypted()) {
                $file->setSourceFile($file->getTargetFile());
            }
        }

        $files            = $resource->getEncryptedPaths();
        $io               = $backend->getIo();
        $metadata         = $backend->getMetadata();
        $defaultArguments = $this->getDefaultArguments(isset($options['write']) ? $options['write'] : false, $io->isVerbose());

        if (!$metadata->asymmetric) {
            $isOk       = false;

            while (!$isOk) {
                $passphrase = $io->askHiddenResponse('Enter passphrase: ');
                $command    = sprintf('gpg --no-use-agent %s --passphrase "%s" --decrypt-files %s', $defaultArguments, $passphrase, implode(' ', $files));
                try {
                    $this->exec($io, 'Decrypt files', $command, null, false);
                    $isOk       = true;
                } catch (\Exception $e) {
                }
            }
        } else {
            $command = sprintf('gpg %s --decrypt-files %s', $defaultArguments, implode(' ', $files));
            $this->exec($io, 'Decrypt files', $command);
        }

        if (isset($options['write']) && $options['write']) {
            $resource->readTargetContent();
        }

        return $resource;
    }
}
