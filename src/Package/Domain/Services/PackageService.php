<?php

namespace PhpLab\Dev\Package\Domain\Services;

use PhpLab\Core\Domain\Base\BaseCrudService;
use PhpLab\Dev\Package\Domain\Interfaces\Repositories\PackageRepositoryInterface;
use PhpLab\Dev\Package\Domain\Interfaces\Services\PackageServiceInterface;

class PackageService extends BaseCrudService implements PackageServiceInterface
{

    public function __construct(PackageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

}
