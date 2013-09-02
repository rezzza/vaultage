<?php

namespace Rezzza\Vaultage\Resource;

/**
 * FileCollection
 *
 * @uses ResourceInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class FileCollection implements ResourceInterface, \Countable, \IteratorAggregate
{
    /**
     * @var array
     */
    protected $files = array();

    /**
     * @var boolean
     */
    protected $written = false;

    /**
     * @return string
     */
    public function __toString()
    {
        $strs = array();
        foreach ($this->files as $file) {
            $strs[] = (string) $file;
        }

        return implode(chr(10), $file);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->files);
    }

    /**
     * @param File $file file
     */
    public function add(File $file)
    {
        $this->files[] = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->files);
    }

    /**
     * {@inheritdoc}
     */
    public function write()
    {
        if (!$this->written) {
            foreach ($this->files as $file) {
                $file->write();
            }
        }

        $this->written = true;
    }

    /**
     * Read target content from the resource.
     */
    public function readTargetContent()
    {
        foreach ($this->files as $file) {
            $file->readTargetContent();
        }

        $this->written = true;
    }

    /**
     * @return array<string>
     */
    public function getDecryptedPaths()
    {
        $paths = array();

        foreach ($this->files as $file) {
            if ($file->isCrypted()) {
                $paths[] = $file->getTargetFile();
            } else {
                $paths[] = $file->getSourceFile();
            }
        }

        return $paths;
    }

    /**
     * @return array<string>
     */
    public function getEncryptedPaths()
    {
        $paths = array();

        foreach ($this->files as $file) {
            if (!$file->isCrypted()) {
                $paths[] = $file->getTargetFile();
            } else {
                $paths[] = $file->getSourceFile();
            }
        }

        return $paths;
    }
}
