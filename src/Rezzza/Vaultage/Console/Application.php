<?php

namespace Rezzza\Vaultage\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Rezzza\Vaultage\Vaultage;
use Rezzza\Vaultage\IO\ConsoleIO;
use Rezzza\Vaultage\IO\IOInterface;
use Rezzza\Vaultage\Console\Command;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Application extends BaseApplication
{
    /**
     * @var IOInterface
     */
    private $io;

    /**
     * Constructor.
     */
    public function __construct()
    {
        error_reporting(-1);

        parent::__construct('Vaultage version', Vaultage::VERSION);

        $this->add(new Command\CompileCommand());
        $this->add(new Command\DecryptCommand());
        $this->add(new Command\DiffCommand());
        $this->add(new Command\EncryptCommand());
        $this->add(new Command\InitializeCommand());
        $this->add(new Command\SelfUpdateCommand());
    }

    /**
     * {@inheritDoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->io = new ConsoleIO($input, $output, $this->getHelperSet());

        if (version_compare(PHP_VERSION, '5.3.3', '<')) {
            $output->writeln('<warning>Vaultage only officially supports PHP 5.3.2 and above, you will most likely encounter problems with your PHP '.PHP_VERSION.', upgrading is strongly recommended.</warning>');
        }

        return parent::doRun($input, $output);
    }

    /**
     * @return IOInterface
     */
    public function getIo()
    {
        return $this->io;
    }

}
