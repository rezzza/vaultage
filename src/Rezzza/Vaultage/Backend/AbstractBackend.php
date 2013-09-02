<?php

namespace Rezzza\Vaultage\Backend;

use Rezzza\Vaultage\IO\IOInterface;
use Rezzza\Vaultage\Dumper\ArrayDumper;
use Rezzza\Vaultage\Resource\ResourceInterface;

abstract class AbstractBackend
{
    /**
     * @var Io\IOInterface
     */
    protected $io;

    /**
     * @var MetadataInterface
     */
    protected $metadata;

    /**
     * @var array
     */
    protected $resourceProcessors = array();

    /**
     * {@inheritdoc}
     */
    public function encrypt(ResourceInterface $resource, array $options = array())
    {
        foreach ($this->resourceProcessors as $resourceProcessor) {
            if ($resourceProcessor->accepts($resource))  {
                return $resourceProcessor->encrypt($resource, $this, $options);
            }
        }

        throw new \Exception(sprintf('There is no resourceProcessor available for Resource "%s" on this backend.', get_class($resource)));
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt(ResourceInterface $resource, array $options = array())
    {
        foreach ($this->resourceProcessors as $resourceProcessor) {
            if ($resourceProcessor->accepts($resource))  {
                return $resourceProcessor->decrypt($resource, $this, $options);
            }
        }

        throw new \Exception(sprintf('There is no resourceProcessor available for Resource "%s" on this backend.', get_class($resource)));
    }

    /**
     * @param ResourceProcessorInterface $resourceProcessor resourceProcessor
     */
    public function addResourceProcessor(ResourceProcessorInterface $resourceProcessor)
    {
        $this->resourceProcessors[] = $resourceProcessor;
    }

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
     * @return IOInterface
     */
    public function getIo()
    {
        return $this->io;
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
     * @return MetadataInterface
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}
