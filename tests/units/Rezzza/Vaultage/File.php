<?php

namespace tests\units\Rezzza\Vaultage;

require __DIR__."/../../../../vendor/autoload.php";

use mageekguy\atoum;
use Rezzza\Vaultage\File as TestedClass;

class File extends atoum\test
{
    public function testGetters()
    {
        $this->if($file = new TestedClass('from', 'to', 'directory'))
            ->string($file->getFrom())
                ->isEqualTo('from')
            ->string($file->getFrom(TestedClass::RELATIVE_PATH))
                ->isEqualTo('from')
            ->string($file->getFrom(TestedClass::ABSOLUTE_PATH))
                ->isEqualTo('directory/from')
            ->string($file->getTo())
                ->isEqualTo('to')
            ->string($file->getTo(TestedClass::RELATIVE_PATH))
                ->isEqualTo('to')
            ->string($file->getTo(TestedClass::ABSOLUTE_PATH))
                ->isEqualTo('directory/to')
            ;
    }
}
