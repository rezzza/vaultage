<?php

namespace Rezzza\Vaultage\Backend;

use Rezzza\Vaultage\IO\IOInterface;

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

    public function encrypt();
    public function decrypt();
    public function initialize($configurationFile);
    public function read($path);
}
