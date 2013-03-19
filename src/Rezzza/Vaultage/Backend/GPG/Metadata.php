<?php

namespace Rezzza\Vaultage\Backend\GPG;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Rezzza\Vaultage\File;
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
    public $files = array();
    public $recipients = array();

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

        foreach ($data['files'] as $file) {
            $this->addFile(new File($file, null, getcwd()));
        }

        $this->recipients = $data['recipients'];
    }

    /**
     * @param string $from from
     * @param string $to   to
     *
     * @return Metadata
     */
    public function addFile(File $file)
    {
        $this->files[] = $file;

        return $this;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
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
            $data['files'][] = $file->getFrom();
        }

        if ($this->asymmetric) {
            $data['recipients'] = $this->recipients;
        }

        return $data;
    }

    /**
     * @param string $name name
     *
     * @return File|null
     */
    public function findDecryptedFile($name)
    {
        // same files.
        return $this->findCryptedFile($name);
    }

    /**
     * @param string $name name
     *
     * @return File|null
     */
    public function findCryptedFile($name)
    {
        foreach ($this->files as $file) {
            if ($file->getFrom() == $name) {
                return $file;
            }
        }
    }
}
