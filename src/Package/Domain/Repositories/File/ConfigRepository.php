<?php

namespace PhpLab\Dev\Package\Domain\Repositories\File;

use PhpLab\Dev\Package\Domain\Entities\ConfigEntity;
use PhpLab\Dev\Package\Domain\Interfaces\Repositories\ConfigRepositoryInterface;

class ConfigRepository implements ConfigRepositoryInterface
{

    public function tableName() : string
    {
        return 'package_config';
    }

    public function getEntityClass() : string
    {
        return ConfigEntity::class;
    }


}

