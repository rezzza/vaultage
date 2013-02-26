<?php

namespace Rezzza\Vaultage\Compiler;

use Symfony\Component\Finder\Finder;

/**
 * Compiler
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Compiler
{
    public function compile($pharFile = 'vaultage.phar')
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $phar = new \Phar($pharFile, 0, 'vaultage.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        $phar->startBuffering();

        // CLI Component files
        foreach ($this->getFiles() as $file) {
            $path = str_replace(__DIR__.'/', '', $file);
            $phar->addFromString($path, file_get_contents($file));
        }
        $this->addVaultage($phar);
        $this->unsetCompileCommand($phar);

        $phar->setStub($this->getStub());

        $phar->stopBuffering();

        unset($phar);

        chmod($pharFile, 0777);
    }

    protected function addVaultage(\Phar $phar)
    {
        $content = file_get_contents(__DIR__ . '/../../../../vaultage');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);

        $phar->addFromString('vaultage', $content);
    }

    protected function unsetCompileCommand(\Phar $phar)
    {
        $content = file_get_contents(__DIR__ . '/../Console/Application.php');
        $content = preg_replace('{\$this\-\>add\(new CompileCommand\(\)\)\;\s*}', '', $content);

        $phar->addFromString('src/Rezzza/Vaultage/Console/Application.php', $content);
    }

    protected function getStub()
    {
        return "#!/usr/bin/env php\n<?php Phar::mapPhar('vaultage.phar'); require 'phar://vaultage.phar/vaultage'; __HALT_COMPILER();";
    }

    protected function getFiles()
    {
        $iterator = Finder::create()->files()->name('*.php')->in(array('vendor', 'src'));

        return array_merge(array('vaultage'), iterator_to_array($iterator));
    }
}
