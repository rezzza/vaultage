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

    public function ask($question, $default = null)
    {
        return $this->helperSet->get('dialog')->ask($this->output, $question, $default);
    }

    public function askConfirmation($question, $default = true)
    {
        return $this->helperSet->get('dialog')->askConfirmation($this->output, $question, $default);
    }
}
