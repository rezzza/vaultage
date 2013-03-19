<?php

namespace Rezzza\Vaultage\IO;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\HelperSet;

/**
 * ConsoleIO
 *
 * @uses IOInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class ConsoleIO implements IOInterface
{
    protected $input;
    protected $output;
    protected $helperSet;

    /**
     * Constructor.
     *
     * @param InputInterface  $input     The input instance
     * @param OutputInterface $output    The output instance
     * @param HelperSet       $helperSet The helperSet instance
     */
    public function __construct(InputInterface $input, OutputInterface $output, HelperSet $helperSet)
    {
        $this->input     = $input;
        $this->output    = $output;
        $this->helperSet = $helperSet;
    }

    public function getInputOption($option)
    {
        return $this->input->getOption($option);
    }

    public function writeln($string)
    {
        return $this->output->writeln($string);
    }

    public function write($string)
    {
        return $this->output->write($string);
    }

    public function isVerbose()
    {
        return $this->getInputOption('verbose');
    }

    public function askAndRepeatHidden($ask, $secondAsk)
    {
        $dialog = $this->helperSet->get('dialog');

        while (true) {
            $data       = $dialog->askHiddenResponse($this->output, $ask);
            $secondData = $dialog->askHiddenResponse($this->output, $secondAsk);

            if ($secondData == $data) {
                return $data;
            } else {
                $this->output->writeln('<error>Two strs are not identical, retry: </error>');
            }
        }
    }

    public function ask($question, $default = null, array $autocomplete = null)
    {
        return $this->helperSet->get('dialog')->ask($this->output, $question, $default, $autocomplete);
    }

    public function askHiddenResponse($question)
    {
        return $this->helperSet->get('dialog')->askHiddenResponse($this->output, $question);
    }

    public function askConfirmation($question, $default = true)
    {
        return $this->helperSet->get('dialog')->askConfirmation($this->output, $question, $default);
    }

    public function askAndValidate($question, $validator, $attempts = false, $default = null, $choices = array())
    {
        return $this->helperSet->get('dialog')->askAndValidate($this->output, $question, $validator, $attempts, $default, $choices);
    }
}
