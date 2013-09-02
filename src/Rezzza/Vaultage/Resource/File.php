<?php

namespace Rezzza\Vaultage\Resource;

/**
 * File
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class File implements ResourceInterface
{
    /**
     * @var string
     */
    protected $encryptedExtension;

    /**
     * @var string
     */
    protected $sourceFile;

    /**
     * @var string
     */
    protected $targetContent;

    /**
     * @var boolean
     */
    protected $isCrypted;

    public function __construct($sourceFile, $encryptedExtension)
    {
        $this->encryptedExtension = $encryptedExtension;
        $this->setSourceFile($sourceFile);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $str = array();
        $str[] = sprintf('<comment>=================== %s =================</comment>', $this->sourceFile);
        $str[] = $this->getTargetContent();

        return implode(chr(10), $str);
    }

    /**
     * @param string $sourceFile sourceFile
     */
    public function setSourceFile($sourceFile)
    {
        $this->sourceFile    = $sourceFile;
        $this->isCrypted     = (bool) preg_match(sprintf('/\.%s$/', $this->encryptedExtension), $this->sourceFile);
        $this->targetContent = null;
    }

    /**
     * @return boolean
     */
    public function isCrypted()
    {
        return $this->isCrypted;
    }

    /**
     * @return string
     */
    public function getSourceFile()
    {
        return $this->sourceFile;
    }

    /**
     * @return string
     */
    public function getSourceContent()
    {
        if (!is_readable($this->sourceFile)) {
            throw new ResourceException(sprintf('File "%s" is not readable', $this->sourceFile));
        }

        return file_get_contents($this->sourceFile);
    }

    /**
     * Transform source file into target file
     *
     * @return string
     */
    public function getTargetFile()
    {
        if ($this->isCrypted()) {
            return substr($this->getSourceFile(), 0, (strlen($this->encryptedExtension) + 1) * -1);
        }

        return $this->getSourceFile().'.'.$this->encryptedExtension;
    }

    /**
     * Read target content from the resource.
     */
    public function readTargetContent()
    {
        $this->setTargetContent(file_get_contents($this->getTargetFile()));
    }

    /**
     * @param string $v v
     */
    public function setTargetContent($v)
    {
        $this->targetContent = $v;
    }

    /**
     * @return string
     */
    public function getTargetContent()
    {
        return $this->targetContent;
    }

    /**
     * write target content on target file.
     */
    public function write()
    {
        $content = $this->getTargetContent();
        if (null === $content) {
            throw new \LogicException('Content is empty, nothing to write.');
        }

        return file_put_contents($this->getTargetFile(), $content);
    }
}
