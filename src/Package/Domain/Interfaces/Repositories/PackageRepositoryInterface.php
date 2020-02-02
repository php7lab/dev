<?php

namespace PhpLab\Dev\Package\Domain\Interfaces\Repositories;

use PhpLab\Core\Domain\Interfaces\GetEntityClassInterface;
use PhpLab\Core\Domain\Interfaces\ReadAllInterface;
use PhpLab\Core\Domain\Interfaces\Repository\ReadOneInterface;
use PhpLab\Core\Domain\Interfaces\Repository\RelationConfigInterface;
use PhpLab\Core\Domain\Interfaces\Repository\RepositoryInterface;

interface PackageRepositoryInterface extends RepositoryInterface, GetEntityClassInterface, ReadAllInterface, ReadOneInterface, RelationConfigInterface
{

}
