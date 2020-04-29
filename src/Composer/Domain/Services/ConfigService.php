<?php

namespace PhpLab\Dev\Composer\Domain\Services;

use Illuminate\Support\Collection;
use PhpLab\Core\Domain\Base\BaseCrudService;
use PhpLab\Core\Domain\Libs\Query;
use PhpLab\Core\Libs\Store\StoreFile;
use PhpLab\Dev\Package\Domain\Entities\ConfigEntity;
use PhpLab\Dev\Package\Domain\Entities\PackageEntity;
use PhpLab\Dev\Composer\Domain\Interfaces\Repositories\ConfigRepositoryInterface;
use PhpLab\Dev\Package\Domain\Interfaces\Repositories\PackageRepositoryInterface;
use PhpLab\Dev\Composer\Domain\Interfaces\Services\ConfigServiceInterface;

class ConfigService extends BaseCrudService implements ConfigServiceInterface
{

    private $packageRepository;

    public function __construct(ConfigRepositoryInterface $repository, PackageRepositoryInterface $packageRepository)
    {
        $this->repository = $repository;
        $this->packageRepository = $packageRepository;
    }

    public function all(Query $query = null)
    {
        /** @var Collection | PackageEntity[] $packageCollection */
        $packageCollection = $this->packageRepository->all();
        $configCollection = new Collection;
        foreach ($packageCollection as $packageEntity) {
            $composerConfigFile = $packageEntity->getDirectory() . '/composer.json';
            $composerConfigStore = new StoreFile($composerConfigFile);
            $composerConfig = $composerConfigStore->load();
            $confiEntity = new ConfigEntity;
            $confiEntity->setId($packageEntity->getId());
            $confiEntity->setConfig($composerConfig);
            $confiEntity->setPackage($packageEntity);
            //EntityHelper::setAttributes($confiEntity, ComposerConfigMapper::arrayToEntity($composerConfig));
            $configCollection->add($confiEntity);
        }
        return $configCollection;
    }

    public function allWithThirdParty(Query $query = null)
    {
        /** @var Collection | PackageEntity[] $packageCollection */
        $packageCollection = $this->packageRepository->allWithThirdParty();
        //dd($packageCollection);
        $configCollection = new Collection;
        foreach ($packageCollection as $packageEntity) {
            $composerConfigFile = $packageEntity->getDirectory() . '/composer.json';
            $composerConfigStore = new StoreFile($composerConfigFile);
            $composerConfig = $composerConfigStore->load();
            $confiEntity = new ConfigEntity;
            $confiEntity->setId($packageEntity->getId());
            $confiEntity->setConfig($composerConfig);
            $confiEntity->setPackage($packageEntity);
            //EntityHelper::setAttributes($confiEntity, ComposerConfigMapper::arrayToEntity($composerConfig));
            $configCollection->add($confiEntity);
        }
        return $configCollection;
    }

}
