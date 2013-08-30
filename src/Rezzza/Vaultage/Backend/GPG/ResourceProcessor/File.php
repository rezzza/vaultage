<?php

namespace Rezzza\Vaultage\Backend\GPG\ResourceProcessor;

use Rezzza\Vaultage\Backend\BackendInterface;
use Rezzza\Vaultage\Backend\ResourceProcessorInterface;
use Rezzza\Vaultage\Resource\ResourceInterface;
use Rezzza\Vaultage\Resource\File as Resource;

class File extends AbstractResourceProcessor implements ResourceProcessorInterface
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
        $pathDecrypted    = $resource->isCrypted() ? $resource->getTargetFile() : $resource->getSourceFile();

        if ($metadata->asymmetric) {
            $recipientsString = '';
            foreach ($metadata->recipients as $recipient) {
                $recipientsString .= sprintf('-r "%s" ', $recipient);
            }

            $command = sprintf('gpg %s %s -e < %s', $defaultArguments, $recipientsString, $pathDecrypted);

            $resource->setTargetContent(
                $this->exec($io, 'Encrypt files', $command)
            );
        } else {
            $passphrase = $io->askAndRepeatHidden('Enter passphrase: ', 'Confirm passphrase: ');
            $command = sprintf('gpg %s --no-use-agent --passphrase "%s" -c < %s', $defaultArguments, $passphrase, $pathDecrypted);
            $resource->setTargetContent(
                $this->exec($io, sprintf('Encrypt file: %s', $pathDecrypted), $command, null, false)
            );
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

        $pathEncrypted    = $resource->isCrypted() ? $resource->getSourceFile() : $resource->getTargetFile();
        $io               = $backend->getIo();
        $metadata         = $backend->getMetadata();
        $defaultArguments = $this->getDefaultArguments(isset($options['write']) ? $options['write'] : false, $io->isVerbose());

        if (!$metadata->asymmetric) {
            $isOk       = false;

            while (!$isOk) {
                $passphrase = $io->askHiddenResponse('Enter passphrase: ');
                $command    = sprintf('gpg --no-use-agent %s --passphrase "%s" -d < %s', $defaultArguments, $passphrase, $pathEncrypted);
                try {
                    $resource->setTargetContent(
                        $this->exec($io, 'Decrypt files', $command, null, false)
                    );
                    $isOk       = true;
                } catch (\Exception $e) {
                }
            }
        } else {
            $command    = sprintf('gpg --no-verbose -d < %s', $pathEncrypted);

            $resource->setTargetContent(
                $this->exec($io, 'Decrypt file', $command, null, false)
            );
        }

        return $resource;
    }
}
