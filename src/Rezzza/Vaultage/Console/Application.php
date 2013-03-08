<?php

namespace Rezzza\Vaultage\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Rezzza\Vaultage\Vaultage;
use Rezzza\Vaultage\Console\Command\ChangeKeyCommand;
use Rezzza\Vaultage\Console\Command\CompileCommand;
use Rezzza\Vaultage\Console\Command\DecryptCommand;
use Rezzza\Vaultage\Console\Command\EncryptCommand;
use Rezzza\Vaultage\Console\Command\SelfUpdateCommand;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Application extends BaseApplication
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        error_reporting(-1);

        parent::__construct('Vaultage version', Vaultage::VERSION);

        $this->add(new EncryptCommand());
        $this->add(new DecryptCommand());
        $this->add(new SelfUpdateCommand());
        $this->add(new CompileCommand());
    }
}
