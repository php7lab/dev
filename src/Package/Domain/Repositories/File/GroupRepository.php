<?php

namespace PhpLab\Dev\Package\Domain\Repositories\File;

use PhpLab\Core\Domain\Helpers\EntityHelper;
use PhpLab\Core\Domain\Interfaces\Entity\EntityIdInterface;
use PhpLab\Core\Domain\Interfaces\Repository\ReadRepositoryInterface;
use PhpLab\Core\Domain\Libs\Query;
use PhpLab\Core\Libs\Store\StoreFile;
use PhpLab\Dev\Package\Domain\Entities\GroupEntity;

class GroupRepository implements ReadRepositoryInterface
{

    private $fileName;

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public function all(Query $query = null)
    {
        $store = new StoreFile($this->fileName);
        $array = $store->load();
        //$collection = $this->forgeEntityCollection($array);
        //return $collection;

        $entityClass = $this->getEntityClass();
        return EntityHelper::createEntityCollection($entityClass, $array);
    }

    public function count(Query $query = null): int
    {
        $collection = $this->all($query);
        return $collection->count();
    }

    public function oneById($id, Query $query = null): EntityIdInterface
    {
        // TODO: Implement oneById() method.
    }

    public function getEntityClass(): string
    {
        return GroupEntity::class;
    }

    public function relations()
    {
        return [];
    }

}
