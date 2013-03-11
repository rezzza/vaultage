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

        $keyData = parse_url($data['key']);
        if (isset($keyData['scheme'])) {
            if ($keyData['scheme'] == 'file') {
                $this->keyFile = $data['key'];
                $path = $keyData['host'];
                if (isset($keyData['path'])) {
                    $path .= $keyData['path'];
                }

                $key  = file_get_contents(str_replace('~', getenv('HOME'), $path));
            } else {
                throw new \InvalidArgumentException('Key only accept file:// scheme, or key directly');
            }
        } else {
            $key = $keyData['path'];
        }

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
