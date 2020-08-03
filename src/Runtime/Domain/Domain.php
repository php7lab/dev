<?php

namespace PhpLab\Dev\Runtime\Domain;

use PhpLab\Core\Domain\Interfaces\DomainInterface;

class Domain implements DomainInterface
{

    public function getName()
    {
        return 'runtime';
    }

}