<?php

namespace Rezzza\Vaultage\Backend;

/**
 * MetadataInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface MetadataInterface
{
    /**
     * @param string $configuration configuration
     * @param array  $data          data
     */
    public function build($configuration, array $data);

    /**
     * @return array<string>
     */
    public function getFiles();

    /**
     * @return string
     */
    public function getEncryptedExtension();

    /**
     * @return boolean
     */
    public function exportConfiguration();
}
