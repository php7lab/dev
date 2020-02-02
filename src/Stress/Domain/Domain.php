<?php

namespace PhpLab\Dev\Stress\Domain;

use PhpLab\Core\Domain\Interfaces\DomainInterface;

class Domain implements DomainInterface
{

    public function getName()
    {
        return 'dev';
    }

}