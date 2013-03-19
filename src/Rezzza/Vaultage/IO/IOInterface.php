<?php

namespace Rezzza\Vaultage\IO;

/**
 * IOInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface IOInterface
{
    public function ask($question, $default = null, array $autocomplete = null);
    public function askAndRepeatHidden($ask, $secondAsk);
    public function askAndValidate($question, $validator, $attempts = false, $default = null, $choices = array());
    public function askConfirmation($question, $default = true);
    public function getInputOption($option);
    public function write($string);
    public function writeln($string);
    public function isVerbose();
}
