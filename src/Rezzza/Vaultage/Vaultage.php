<?php

namespace Rezzza\Vaultage;

use Rezzza\Vaultage\Parser\JsonParser;
use Rezzza\Vaultage\Parser\ParserInterface;

/**
 * Vaultage
 *
 * @author Stephane PY <py.stephane1@gmail.com> 
 */
class Vaultage
{
    /**
     * @var Metadata
     */
    protected $metadata;

    /**
     * @param string          $file   file
     * @param ParserInterface $parser parser
     */
    public function __construct($file, ParserInterface $parser = null)
    {
        if (null === $parser) {
            $parser = new JsonParser();
        }

        if (!$parser instanceof ParserInterface) {
            throw new \InvalidArgumentException(sprintf('Parser "%s" has to implements ParserInterface', get_class($parser)));
        }

        $this->metadata = $parser->parse($file);
    }
}
