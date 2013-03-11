<?php

namespace Rezzza\Vaultage;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Metadata
 *
 * @author Stephane PY <py.stephane1@gmail.com> 
 */
class Metadata
{
    public $configuration;
    public $keyFile;
    public $key;
    public $needsPassphrase = false;
    public $passphrase;

    protected $files = array();

    /**
     * @param string $configuration path to configuration
     */
    public function __construct($configuration)
    {
        $this->configuration = $configuration;
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
     * @param array $data data
     */
    public function buildFromArray(array $data)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(array('key', 'files'));
        $resolver->setDefaults(array(
            'passphrase' => false,
        ));
        $resolver->setAllowedTypes(array(
            'key'        => 'string',
            'files'      => 'array',
        ));

        $data = $resolver->resolve($data);
        $key  = $data['key'];

        if (strpos($key, 'file://') === 0) {
            $this->keyFile = $key;
            $absoluteKeyFile = $this->getAbsoluteKeyFile();
            if (file_exists($absoluteKeyFile)) {
                $key = file_get_contents($this->getAbsoluteKeyFile());
            }
        }

        $this->key = $key;

        if (!$key) {
            throw new \LogicException(sprintf('Key cannot be retrieved (path = "%s")', $data['key']));
        }

        $this->key             = $key;
        $this->needsPassphrase = $data['passphrase'];

        foreach ($data['files'] as $from => $to) {
            $this->addFile(new File($from, $to, getcwd()));
        }
    }

    /**
     * @return string
     */
    public function getAbsoluteKeyFile()
    {
        if (!$this->keyFile) {
            return null;
        }

        $keyData = parse_url($this->keyFile);
        $path = $keyData['host'];
        if (isset($keyData['path'])) {
            $path .= $keyData['path'];
        }

        return str_replace('~', getenv('HOME'), $path);
    }

    /**
     * Export metadatas to configuration format
     * 
     * @return array
     */
    public function exportConfiguration()
    {
        $data = array(
            'key'        => (null !== $this->keyFile) ? $this->keyFile : $this->key,
            'passphrase' => $this->needsPassphrase,
            'files'      => array(),
        );

        foreach ($this->files as $file) {
            $data['files'][$file->getFrom()] = $file->getTo();
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
        foreach ($this->files as $file) {
            if ($file->getFrom() == $name) {
                return $file;
            }
        }
    }

    /**
     * @param string $name name
     * 
     * @return File|null
     */
    public function findCryptedFile($name)
    {
        foreach ($this->files as $file) {
            if ($file->getTo() == $name) {
                return $file;
            }
        }
    }
}
