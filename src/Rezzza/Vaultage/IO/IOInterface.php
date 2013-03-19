<?php

namespace Rezzza\Vaultage\IO;

/**
 * IOInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface IOInterface
{
    public function ask($question, $default = null);
    public function askAndRepeatHidden($ask, $secondAsk);
    public function askConfirmation($question, $default = true);
    public function getInputOption($option);
    public function writeln($string);

}
