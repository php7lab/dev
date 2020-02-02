<?php

namespace PhpLab\Dev\Package\Domain\Repositories\File;

use Illuminate\Support\Collection;
use PhpLab\Core\Legacy\Yii\Helpers\FileHelper;
use PhpLab\Core\Domain\Data\Query;
use PhpLab\Core\Domain\Base\BaseRepository;
use PhpLab\Dev\Package\Domain\Entities\GroupEntity;
use PhpLab\Dev\Package\Domain\Entities\PackageEntity;
use PhpLab\Dev\Package\Domain\Interfaces\Repositories\PackageRepositoryInterface;

class PackageRepository extends BaseRepository implements PackageRepositoryInterface
{

    const VENDOR_DIR = __DIR__ . '/../../../../../../..';

    protected $tableName = '';
    protected $entityClass = PackageEntity::class;
    private $groupRepostory;

    public function __construct(GroupRepository $groupRepostory)
    {
        $this->groupRepostory = $groupRepostory;
    }

    public function all(Query $query = null)
    {
        $vendorDir = realpath(self::VENDOR_DIR);
        /** @var GroupEntity[] $groupCollection */
        $groupCollection = $this->groupRepostory->all();
        $collection = new Collection;
        foreach ($groupCollection as $groupEntity) {
            $dir = $vendorDir . DIRECTORY_SEPARATOR . $groupEntity->name;
            $names = FileHelper::scanDir($dir);
            foreach ($names as $name) {
                $packageEntity = new PackageEntity;
                $packageEntity->setName($name);
                $packageEntity->setGroup($groupEntity);
                $collection->add($packageEntity);
            }
        }
        return $collection;
    }

    public function count(Query $query = null): int
    {
        return count($this->all($query));
    }

    public function oneById($id, Query $query = null)
    {
        // TODO: Implement oneById() method.
    }

    public function relations()
    {
        // TODO: Implement relations() method.
    }
}
