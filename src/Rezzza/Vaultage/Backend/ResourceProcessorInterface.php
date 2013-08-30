<?php

namespace Rezzza\Vaultage\Backend;

use Rezzza\Vaultage\Resource\ResourceInterface;
use Rezzza\Vaultage\Backend\BackendInterface;

interface ResourceProcessorInterface
{
    public function accepts(ResourceInterface $resource);

    public function encrypt(ResourceInterface $resource, BackendInterface $backend);
    public function decrypt(ResourceInterface $resource, BackendInterface $backend);
}
