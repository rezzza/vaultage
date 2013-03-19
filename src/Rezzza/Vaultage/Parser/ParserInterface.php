<?php

namespace Rezzza\Vaultage\Parser;

/**
 * ParserInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface ParserInterface
{
    /**
     * @param string $file
     */
    public function parse($file);
}
