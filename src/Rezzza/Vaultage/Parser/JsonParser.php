<?php

namespace Rezzza\Vaultage\Parser;

use Rezzza\Vaultage\Metadata;
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
    public function parse(Metadata $metadata)
    {
        $path    = $metadata->configuration;
        $content = file_get_contents($path);

        if (false === $content) {
            throw new ResourceException(sprintf('File "%s" is not exists or is not readable', $path));
        }

        $content = json_decode(file_get_contents($path), true);

        if (null === $content) {
            throw new ResourceException(sprintf('File "%s" is not at JSON format', $path));
        }

        return $metadata->buildFromArray($content);
    }
}
