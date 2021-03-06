<?php

namespace PhpLab\Dev\Package\Domain\Repositories\File;

use Illuminate\Support\Collection;
use PhpLab\Core\Domain\Interfaces\Entity\EntityIdInterface;
use PhpLab\Core\Domain\Libs\Query;
use PhpLab\Core\Legacy\Yii\Helpers\FileHelper;
use PhpLab\Core\Libs\Store\StoreFile;
use PhpLab\Dev\Package\Domain\Entities\GroupEntity;
use PhpLab\Dev\Package\Domain\Entities\PackageEntity;
use PhpLab\Dev\Package\Domain\Interfaces\Repositories\PackageRepositoryInterface;

class PackageRepository implements PackageRepositoryInterface
{

    const VENDOR_DIR = __DIR__ . '/../../../../../../..';

    protected $tableName = '';
    private $groupRepostory;

    public function __construct(GroupRepository $groupRepostory)
    {
        $this->groupRepostory = $groupRepostory;
    }

    public function getEntityClass(): string
    {
        return PackageEntity::class;
    }

    public function allWithThirdParty(Query $query = null)
    {
        $vendorDir = realpath(self::VENDOR_DIR);

        $groups = FileHelper::scanDir($vendorDir);
        /** @var GroupEntity[] $groupCollection */
        $groupCollection = new Collection;

        foreach ($groups as $group) {
            if(is_dir($vendorDir . '/' . $group)) {
                $groupEntity = new GroupEntity;
                $groupEntity->name = $group;
                $groupCollection->add($groupEntity);
            }
        }

        $collection = new Collection;
        foreach ($groupCollection as $groupEntity) {
            $dir = $vendorDir . DIRECTORY_SEPARATOR . $groupEntity->name;

            $names = FileHelper::scanDir($dir);

            foreach ($names as $name) {
                $packageEntity = new PackageEntity;
                $packageEntity->setName($name);
                $packageEntity->setGroup($groupEntity);

                if ($this->isComposerPackage($packageEntity)) {
                    $collection->add($packageEntity);
                }

            }

        }

        return $collection;
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
                if ($this->isComposerPackage($packageEntity)) {
                    $collection->add($packageEntity);
                }
            }
        }
        return $collection;
    }

    private function isComposerPackage(PackageEntity $packageEntity): bool {
        $composerConfigFile = $packageEntity->getDirectory() . '/composer.json';
        $isPackage = is_dir($packageEntity->getDirectory()) && is_file($composerConfigFile);
        return $isPackage;
    }

    public function count(Query $query = null): int
    {
        return count($this->all($query));
    }

    public function oneById($id, Query $query = null): EntityIdInterface
    {
        // TODO: Implement oneById() method.
    }

    public function relations()
    {
        // TODO: Implement relations() method.
    }
}
