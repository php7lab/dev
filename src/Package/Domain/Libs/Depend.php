<?php

namespace PhpLab\Dev\Package\Domain\Libs;

use PhpLab\Core\Libs\Store\StoreFile;
use yii2rails\domain\enums\RelationEnum;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use PhpLab\Core\Legacy\Yii\Helpers\FileHelper;
use PhpLab\Dev\Package\Domain\Entities\ConfigEntity;
use PhpLab\Dev\Package\Domain\Helpers\ComposerConfigHelper;
use PhpLab\Dev\Package\Domain\Interfaces\Services\ConfigServiceInterface;
use PhpLab\Dev\Package\Domain\Interfaces\Services\GitServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use yii\helpers\ArrayHelper;
use Symfony\Component\Console\Helper\ProgressBar;

class Depend
{

    private $namespacesPackages = [];
    private $lastVersions = [];
    private $installedVersions = [];

    public function __construct($namespacesPackages, $lastVersions)
    {
        $this->namespacesPackages = $namespacesPackages;
        $this->lastVersions = $lastVersions;

        $store = new StoreFile(__DIR__ . '/../../../../../../composer/installed.json');
        $installed = $store->load();
        foreach ($installed as $item) {
            $this->installedVersions[$item['name']] = $item['version'];
        }
    }

    public function all($collection, callable $callback): array
    {
        $deps = [];
        foreach ($collection as $configEntity) {
            $callback();
            $dep = $this->item($configEntity);
            $deps[$configEntity->getId()] = $dep;
        }
        return $deps;
    }

    private function getRequiredFromPhpCode(ConfigEntity $configEntity): array {
        $dir = $configEntity->getPackage()->getDirectory();
        $uses = ComposerConfigHelper::getUses($dir);
        $requirePackage = [];
        if($uses) {
            foreach ($uses as $use) {
                foreach ($this->namespacesPackages as $namespacesPackage => $packageEntity) {
                    if (mb_strpos($use, $namespacesPackage) === 0) {
                        $requirePackage[] = $packageEntity->getId();
                    }
                }
            }
            $requirePackage = array_unique($requirePackage);
            $requirePackage = array_values($requirePackage);
        }
        return $requirePackage;
    }

    private function getWanted() {

    }

    private function getRequreUpdate(array $requires) {
        $requireUpdate = [];
        foreach ($this->lastVersions as $packageId => $lastVersion) {
            $currentVersion = ArrayHelper::getValue($requires, $packageId);
            if($currentVersion && $lastVersion && version_compare($currentVersion, $lastVersion, '<')) {
                $requireUpdate[$packageId] = $lastVersion;
            }
        }
    }

    private function item(ConfigEntity $configEntity) {
        //$dep['id'] = $configEntity->getId();
        $dep['all'] = $configEntity->getAllRequire();

        $requirePackage = $this->getRequiredFromPhpCode($configEntity);

        if(!empty($requirePackage)) {
            //$dep['require-package'] = $requirePackage;
            $wanted = ComposerConfigHelper::getWanted($configEntity, $requirePackage);
            $dep['wanted'] = [];
            foreach ($wanted as $packageId) {
                $lastVersion = ArrayHelper::getValue($this->lastVersions, $packageId);
                if(empty($lastVersion)) {
                    $lastVersion = ArrayHelper::getValue($this->installedVersions, $packageId);
                }
                $lastVersion = str_replace('.x-dev', '.*', $lastVersion);
                $dep['wanted'][$packageId] = $lastVersion;
            }
            $requires = $configEntity->getAllRequire();
            if($requires) {
                $dep['update'] = $this->getRequreUpdate($requires);
            }
        }

        //unset($dep['require-package']);
        return $dep;
    }
}