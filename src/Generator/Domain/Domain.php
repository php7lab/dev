<?php

namespace PhpLab\Dev\Generator\Domain;

use PhpLab\Core\Domain\Interfaces\DomainInterface;

class Domain implements DomainInterface
{

    public function getName()
    {
        return 'generator';
    }

}