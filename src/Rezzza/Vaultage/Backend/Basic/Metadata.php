<?php

namespace Rezzza\Vaultage\Backend\Basic;

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
    public $keyFile;
    public $key;
    public $needsPassphrase = false;
    public $passphrase;
    protected $encryptedExtension = 'crypted';

    public $files;

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
        $resolver->setRequired(array('key', 'files'));
        $resolver->setDefaults(array(
            'backend'             => null,
            'passphrase'          => false,
            'encrypted_extension' => 'crypted',
        ));
        $resolver->setAllowedTypes(array(
            'key'                 => ['string'],
            'encrypted_extension' => ['string'],
            'files'               => ['array'],
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

        foreach ($data['files'] as $path) {
            $this->files->add(
                new Resource\File($path, $this->getEncryptedExtension())
            );
        }
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
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
     * {@inheritdoc}
     */
    public function getEncryptedExtension()
    {
        return $this->encryptedExtension;
    }

    public function setEncryptedExtension($v)
    {
        $this->encryptedExtension = $v;
    }

    /**
     * Export metadatas to configuration format
     *
     * @return array
     */
    public function exportConfiguration()
    {
        $data = array(
            'backend'             => 'basic',
            'key'                 => (null !== $this->keyFile) ? $this->keyFile : $this->key,
            'passphrase'          => $this->needsPassphrase,
            'encrypted_extension' => $this->getEncryptedExtension(),
            'files'               => array(),
        );

        foreach ($this->files as $file) {
            $data['files'][] = $file->getSourceFile();
        }

        return $data;
    }
}
