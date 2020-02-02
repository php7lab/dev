<?php

namespace PhpLab\Dev\Package\Domain\Services;

use PhpLab\Core\Domain\Base\BaseCrudService;
use PhpLab\Dev\Package\Domain\Repositories\File\GroupRepository;

class GroupService extends BaseCrudService
{

    public function __construct(GroupRepository $repository)
    {
        $this->repository = $repository;
    }

}
