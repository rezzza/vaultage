<?php

namespace Rezzza\Vaultage;

/**
 * File
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class File
{
    CONST ABSOLUTE_PATH = true;
    CONST RELATIVE_PATH = false;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $to;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @param string $from      from
     * @param string $to        to
     * @param string $directory directory
     */
    public function __construct($from, $to = null, $directory)
    {
        $this->from         = $from;
        $this->to           = $to;
        $this->directory    = $directory;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->from;
    }

    /**
     * @param boolean $absolute absolute
     *
     * @return string
     */
    public function getFrom($absolute = self::RELATIVE_PATH)
    {
        return $absolute ? $this->directory.DIRECTORY_SEPARATOR.$this->from : $this->from;
    }

    /**
     * @param boolean $absolute absolute
     *
     * @return string
     */
    public function getTo($absolute = self::RELATIVE_PATH)
    {
        return $absolute ? $this->directory.DIRECTORY_SEPARATOR.$this->to : $this->to;
    }
}
