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
     * @param string $path path
     * 
     * @return Metadata
     */
    public function parse($file);
}
