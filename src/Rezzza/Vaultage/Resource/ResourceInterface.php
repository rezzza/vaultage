<?php

namespace Rezzza\Vaultage\Resource;

/**
 * ResourceInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface ResourceInterface
{
    public function __toString();
    public function write();
}
