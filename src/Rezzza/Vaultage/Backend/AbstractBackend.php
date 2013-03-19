<?php

namespace Rezzza\Vaultage\Backend;

use Rezzza\Vaultage\IO\IOInterface;
use Rezzza\Vaultage\Dumper\ArrayDumper;

abstract class AbstractBackend
{
    protected $io;

    protected $metadata;

    protected $cipher;

    CONST ENCRYPT = 'encrypt';
    CONST DECRYPT = 'decrypt';

    /**
     * @param IOInterface $io io
     *
     * @return void
     */
    public function setIO(IOInterface $io)
    {
        $this->io = $io;

        return $this;
    }

    /**
     * @param string $configuration configuration
     * @param array  $data          data
     *
     * @return MetadataInterface
     */
    public function buildMetadatas($configuration, array $data)
    {
        if (!$this->metadata) {
            throw new \LogicException('Metadata property has to be a Metadata object.');
        }

        $metadata = $this->metadata;
        $metadata->build($configuration, $data);

        return $metadata;
    }

    /**
     * @param string $file file
     */
    public function dumpMetadatas($file)
    {
        $data = $this->metadata->exportConfiguration();
        file_put_contents($file, ArrayDumper::toJson($data));
    }

    /**
     * @param string $option option
     *
     * @return mixed
     */
    public function getInputOption($option)
    {
        return $this->io->getInputOption($option);
    }

    /**
     * @param string $type type
     *
     * @return array
     */
    public function getFiles($type = self::ENCRYPT)
    {
        $files = $this->getInputOption('files');

        if (empty($files)) {
            return $this->metadata->getFiles();
        }

        $files  = array_filter(explode(',', $files));
        $data   = array();
        $method = $type === self::ENCRYPT ? 'findDecryptedFile' : 'findCryptedFile';

        foreach ($files as $file) {
            $data[] = $this->metadata->{$method}($file);
        }

        return array_filter($data);
    }
}
