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
    /**
     * @var string
     */
    protected $key;

    /**
     * @var array
     */
    protected $files = array();

    /**
     * @param string  $key key
     * 
     * @return Metadata
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @param string $from from
     * @param string $to   to
     * 
     * @return Metadata
     */
    public function addFile($from, $to)
    {
        $this->files[$from] = $to;

        return $this;
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
        $resolver->setAllowedTypes(array(
            'key'   => 'string',
            'files' => 'array',
        ));


        $data = $resolver->resolve($data);

        $keyData = parse_url($data['key']);
        if (isset($keyData['scheme'])) {
            if ($keyData['scheme'] == 'file') {
                $key = file_get_contents($keyData['host']);
            } else {
                throw new \InvalidArgumentException('Key only accept file:// scheme, or key directly');
            }
        } else {
            $key = $keyData['path'];
        }

        if (!$key) {
            throw new \LogicException(sprintf('Key cannot be retrieved (path = "%s")', $data['key']));
        }

        $metadata = new static();
        $metadata->setKey($key);

        foreach ($data['files'] as $from => $to) {
            $metadata->addFile($from, $to);
        }

        return $metadata;
    }
}
