<?php

namespace PhpLab\Dev\Package\Domain\Services;

use PhpLab\Core\Legacy\Yii\Helpers\ArrayHelper;
use PhpLab\Core\Domain\Base\BaseService;
use PhpLab\Dev\Package\Domain\Entities\CommitEntity;
use PhpLab\Dev\Package\Domain\Entities\PackageEntity;
use PhpLab\Dev\Package\Domain\Entities\TagEntity;
use PhpLab\Dev\Package\Domain\Interfaces\Repositories\GitRepositoryInterface;
use PhpLab\Dev\Package\Domain\Interfaces\Services\GitServiceInterface;
use PhpLab\Dev\Package\Domain\Libs\GitShell;

class GitService extends BaseService implements GitServiceInterface
{

    public function __construct(GitRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function lastVersion(PackageEntity $packageEntity)
    {
        $tags = $this->repository->allVersion($packageEntity);
        if ($tags) {
            return $tags[0];
        }
        return null;
    }

    public function isNeedRelease(PackageEntity $packageEntity): bool
    {
        $commitCollection = $this->repository->allCommit($packageEntity);
        $tagCollection = $this->repository->allTag($packageEntity);

        if ($commitCollection->count() == 0) {
            return true;
        }
        if (count($tagCollection->all()) == 0) {
            return true;
        }

        $commitShaMap = $commitCollection->map(function (CommitEntity $commitEntity) {
            return $commitEntity->getSha();
        });
        $commitShaMap = array_flip($commitShaMap->toArray());

        $tagShaMap = $tagCollection->map(function (TagEntity $tagEntity) {
            return $tagEntity->getSha();
        });
        $tagShaMap = array_flip($tagShaMap->toArray());

        $commitTagMap = [];
        foreach ($commitShaMap as $commitSha => $commitOrder) {
            $tagOrder = ArrayHelper::getValue($tagShaMap, $commitSha);
            $commitTagMap[$commitOrder] = $tagOrder;
        }

        $isNeedRelease = $commitTagMap[0] === null;

        return $isNeedRelease;
    }

    public function pullPackage(PackageEntity $packageEntity)
    {
        $git = new GitShell($packageEntity->getDirectory());
        $result = $git->pullWithInfo();
        if ($result == 'Already up-to-date.') {
            return false;
        } else {
            return $result;
        }
    }

    public function isHasChanges(PackageEntity $packageEntity): bool
    {
        return $this->repository->isHasChanges($packageEntity);
    }

    public function allChanged()
    {
        return $this->repository->allChanged();
    }

}