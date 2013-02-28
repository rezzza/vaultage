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
    public $key;
    public $needsPassphrase = false;
    public $passphrase;

    protected $files = array();

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
     * 
     * @return Metadata
     */
    public static function createFromArray(array $data)
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

        $metadata                  = new static();
        $metadata->key             = $key;
        $metadata->needsPassphrase = $data['passphrase'];

        foreach ($data['files'] as $from => $to) {
            $metadata->addFile(new File($from, $to, getcwd()));
        }

        return $metadata;
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
