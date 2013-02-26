<?php

namespace Rezzza\Vaultage\Parser;

use Rezzza\Vaultage\Metadata;

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
    public function parse($path)
    {
        $content = json_decode(file_get_contents($path), true);

        if (null === $content) {
            throw new \InvalidArgumentException(sprintf('File "%s" is not at JSON format', $path));
        }

        return Metadata::createFromArray($content);
    }
}
