<?php

namespace Rezzza\Vaultage\Backend\Basic\ResourceProcessor;

use Rezzza\Vaultage\Backend\BackendInterface;
use Rezzza\Vaultage\Exception\BadCredentialsException;
use Rezzza\Vaultage\Backend\Basic\Cipher;
use Rezzza\Vaultage\Backend\ResourceProcessorInterface;
use Rezzza\Vaultage\Resource\File as Resource;
use Rezzza\Vaultage\Resource\ResourceInterface;

/**
 * File
 *
 * @uses ResourceProcessorInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class File implements ResourceProcessorInterface
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

        if ($resource->isCrypted()) {
            $resource->setSourceFile($resource->getTargetFile());
        }

        $cipher = new Cipher();

        $resource->setTargetContent(
            $cipher->encrypt($resource->getSourceContent(), $metadata)
        );

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

            if (!$resource->isCrypted()) {
                $resource->setSourceFile($resource->getTargetFile());
            }

            $cipher = new Cipher();

            try {
                $resource->setTargetContent(
                    $cipher->decrypt($resource->getSourceContent(), $metadata)
                );
                $processed = true;
            } catch (\Exception $e) {
                throw new BadCredentialsException('Bad credentials');
            }
        }

        return $resource;
    }
}
