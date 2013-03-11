<?php

namespace Rezzza\Vaultage\Parser;

use Rezzza\Vaultage\Metadata;

/**
 * ParserInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com> 
 */
interface ParserInterface
{
    /**
     * @param Metadata $metadata
     */
    public function parse(Metadata $metadata);
}
