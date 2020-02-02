<?php

namespace PhpLab\Dev\Generator\Domain\Interfaces;

use PhpLab\Dev\Generator\Domain\Dto\BuildDto;

interface DomainServiceInterface
{

    public function generate(BuildDto $buildDto);

}