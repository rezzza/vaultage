<?php

namespace Rezzza\Vaultage;

use Rezzza\Vaultage\Backend\BackendInterface;
use Rezzza\Vaultage\Parser\JsonParser;
use Rezzza\Vaultage\Parser\ParserInterface;

/**
 * Vaultage
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Vaultage
{
    CONST VERSION = '1.0c';

    /**
     * @var BackendInterface
     */
    protected $backend;

    /**
     * @param string          $file   file
     * @param ParserInterface $parser parser
     *
     * @return BackendInterface
     */
    public function buildBackend($file, ParserInterface $parser = null)
    {
        if (null === $parser) {
            $parser = new JsonParser();
        }

        $this->setBackend($parser->parse($file));

        return $this->getBackend();
    }

    /**
     * @param BackendInterface $backend backend
     *
     * @return Vaultage
     */
    public function setBackend(BackendInterface $backend)
    {
        $this->backend = $backend;

        return $this;
    }

    /**
     * @return BackendInterface
     */
    public function getBackend()
    {
        return $this->backend;
    }
}
