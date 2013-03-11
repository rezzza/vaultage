<?php

namespace Rezzza\Vaultage;

use Rezzza\Vaultage\Cipher\Cipher;
use Rezzza\Vaultage\Dumper\ArrayDumper;
use Rezzza\Vaultage\Exception\BadCredentialsException;
use Rezzza\Vaultage\Exception\ResourceException;
use Rezzza\Vaultage\Parser\JsonParser;
use Rezzza\Vaultage\Parser\ParserInterface;

/**
 * Vaultage
 *
 * @author Stephane PY <py.stephane1@gmail.com> 
 */
class Vaultage
{
    CONST VERSION = '0.1';

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
        $this->metadata = new Metadata($file);
    }

    /**
     * @param ParserInterface $parser parser
     */
    public function buildMetadata(ParserInterface $parser = null)
    {
        if (null === $parser) {
            $parser = new JsonParser();
        }

        if (!$parser instanceof ParserInterface) {
            throw new \InvalidArgumentException(sprintf('Parser "%s" has to implements ParserInterface', get_class($parser)));
        }

        $parser->parse($this->metadata);
    }

    /**
     * Dump metadatas to the file.
     */
    public function dumpMetadatas()
    {
        $data = $this->metadata->exportConfiguration();
        file_put_contents($this->metadata->configuration, ArrayDumper::toJson($data));
    }

    /**
     * @param File    $file  file
     * @param boolean $write write
     * 
     * @return string
     */
    public function encrypt(File $file, $write = false)
    {
        $from = new \SplFileInfo($file->getFrom(File::ABSOLUTE_PATH));

        if (!$from->isReadable()) {
            throw new ResourceException(sprintf('File "%s" is not readable', $from));
        }

        $data = $this->getCipher()
            ->encrypt(file_get_contents($from), $this->metadata);

        if ($write) {
            $to = new \SplFileInfo($file->getTo(File::ABSOLUTE_PATH));

            $to->openFile('w+')
                ->fwrite($data);
        }

        return $data;
    }

    /**
     * @throws BadCredentialsException
     *
     * @param File    $file  file
     * @param boolean $write write
     *
     * return string
     */
    public function decrypt(File $file, $write = false)
    {
        $to   = new \SplFileInfo($file->getTo(File::ABSOLUTE_PATH));

        if (!$to->isReadable()) {
            throw new ResourceException(sprintf('File "%s" is not writable', $to));
        }

        $data = $this->getCipher()
            ->decrypt(file_get_contents($to), $this->metadata);

        if ($write) {
            $from = new \SplFileInfo($file->getFrom(File::ABSOLUTE_PATH));
            
            $from->openFile('w+')
                ->fwrite($data);
        }

        return $data;
    }

    /**
     * @return Metadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return Cipher
     */
    public function getCipher()
    {
        return new Cipher();
    }
}
