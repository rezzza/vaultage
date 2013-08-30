<?php

namespace Rezzza\Vaultage\Backend\Basic\ResourceProcessor;

use Rezzza\Vaultage\Backend\BackendInterface;
use Rezzza\Vaultage\Backend\Basic\Cipher;
use Rezzza\Vaultage\Backend\ResourceProcessorInterface;
use Rezzza\Vaultage\Resource\FileCollection as Resource;
use Rezzza\Vaultage\Resource\ResourceInterface;

/**
 * FileCollection
 *
 * @uses ResourceProcessorInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class FileCollection implements ResourceProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function accepts(ResourceInterface $resource)
    {
        return $resource instanceof Resource;
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt(ResourceInterface $resource, BackendInterface $backend, array $options = array())
    {
        $metadata = $backend->getMetadata();

        if ($metadata->needsPassphrase) {
            $metadata->passphrase = $backend->getIo()->askAndRepeatHidden('Enter passphrase: ', 'Confirm passphrase: ');
        }

        $cipher = new Cipher();
        foreach ($resource as $file) {
            if ($file->isCrypted()) {
                $file->setSourceFile($file->getTargetFile());
            }

            $file->setTargetContent(
                $cipher->encrypt($file->getSourceContent(), $metadata)
            );
        }

        return $resource;

    }

    /**
     * {@inheritdoc}
     */
    public function decrypt(ResourceInterface $resource, BackendInterface $backend, array $options = array())
    {
        $processed = false;
        $metadata  = $backend->getMetadata();

        while (!$processed) {
            if ($metadata->needsPassphrase) {
                $metadata->passphrase = $backend->getIo()->askHiddenResponse('Enter passphrase: ');
            } else {
                // there is no way of misstyping, retry will make a infinite loop.
                $processed = true;
            }

            $cipher = new Cipher();
            foreach ($resource as $file) {
                if (!$file->isCrypted()) {
                    $file->setSourceFile($file->getTargetFile());
                }


                try {
                    $file->setTargetContent(
                        $cipher->decrypt($file->getSourceContent(), $metadata)
                    );
                    $processed = true;
                } catch (\Exception $e) {
                }
            }
        }

        return $resource;
    }
}
