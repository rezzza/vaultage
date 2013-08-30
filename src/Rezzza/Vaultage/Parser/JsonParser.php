<?php

namespace Rezzza\Vaultage\Parser;

use Rezzza\Vaultage\Backend\Factory;
use Rezzza\Vaultage\Exception\ResourceException;

/**
 * JsonParser
 *
 * @uses ParserInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class JsonParser implements ParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse($file)
    {
        $content = file_get_contents($file);

        if (false === $content) {
            throw new ResourceException(sprintf('File "%s" is not exists or is not readable', $file));
        }

        $content = json_decode(file_get_contents($file), true);

        if (null === $content) {
            throw new ResourceException(sprintf('File "%s" is not at JSON format', $file));
        }

        $backend = (isset($content['backend'])) ? $content['backend'] : 'basic';
        $backend = Factory::create($backend);
        $backend->buildMetadatas($file, $content);

        return $backend;
    }
}
