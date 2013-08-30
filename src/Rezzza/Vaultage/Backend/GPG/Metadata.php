<?php

namespace Rezzza\Vaultage\Backend\GPG;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Rezzza\Vaultage\Resource;
use Rezzza\Vaultage\Backend\MetadataInterface;

/**
 * Metadata
 *
 * @uses MetadataInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Metadata implements MetadataInterface
{
    public $configuration;
    public $asymmetric = true;
    public $files;
    public $recipients = array();

    protected $encryptedExtension = 'gpg';

    /**
     * constructor
     */
    public function __construct()
    {
        $this->files = new Resource\FileCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function build($configuration, array $data)
    {
        $this->configuration = $configuration;

        $resolver = new OptionsResolver();
        $resolver->setRequired(array('asymmetric', 'files'));
        $resolver->setDefaults(array(
            'backend'    => null,
            'recipients' => array(),
        ));
        $resolver->setAllowedTypes(array(
            'recipients' => 'array',
            'files'      => 'array',
        ));

        $data = $resolver->resolve($data);

        $this->asymmetric = (bool) $data['asymmetric'];

        foreach ($data['files'] as $path) {
            $this->files->add(
                new Resource\File($path, $this->getEncryptedExtension())
            );
        }

        $this->recipients = $data['recipients'];
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * {@inheritdoc}
     */
    public function getEncryptedExtension()
    {
        return $this->encryptedExtension;
    }

    /**
     * Export metadatas to configuration format
     *
     * @return array
     */
    public function exportConfiguration()
    {
        $data = array(
            'backend'    => 'gpg',
            'asymmetric' => $this->asymmetric,
            'files'      => array(),
        );

        foreach ($this->files as $file) {
            $data['files'][] = $file->getSourceFile();
        }

        if ($this->asymmetric) {
            $data['recipients'] = $this->recipients;
        }

        return $data;
    }
}
