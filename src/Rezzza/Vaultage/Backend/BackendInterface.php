<?php

namespace Rezzza\Vaultage\Backend;

use Rezzza\Vaultage\IO\IOInterface;
use Rezzza\Vaultage\Resource\ResourceInterface;

interface BackendInterface
{
    /**
     * @param string $configuration configuration
     * @param array  $data          data
     *
     * @return MetadataInterface
     */
    public function buildMetadatas($configuration, array $data);

    /**
     * @param IOInterface $io io
     *
     * @return BackendInterface
     */
    public function setIO(IOInterface $io);

    public function encrypt(ResourceInterface $resource, array $options = array());
    public function decrypt(ResourceInterface $resource, array $options = array());
    public function initialize($configurationFile);
}
