<?php

use \mageekguy\atoum;

$script->addTestAllDirectory(__DIR__.'/tests/units');

$cliReport = $script->addDefaultReport();
$cliReport->addField(new atoum\report\fields\runner\result\logo());
$runner->addReport($cliReport);
