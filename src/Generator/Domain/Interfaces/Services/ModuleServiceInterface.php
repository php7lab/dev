<?php

namespace PhpLab\Dev\Generator\Domain\Interfaces\Services;

use PhpLab\Dev\Generator\Domain\Dto\BuildDto;

interface ModuleServiceInterface
{

    public function generate(BuildDto $buildDto);

}
