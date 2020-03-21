<?php

namespace PhpLab\Dev\Package\Commands;

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

class ComposerConfigCommand extends Command
{

    protected static $defaultName = 'package:composer:dependency-version';

    protected $configService;
    protected $gitService;

    public function __construct(?string $name = null, ConfigServiceInterface $configService, GitServiceInterface $gitService)
    {
        parent::__construct($name);
        $this->configService = $configService;
        $this->gitService = $gitService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<fg=white># Composer dependency version</>');
        $output->writeln('');
        /** @var ConfigEntity[] | Collection $collection */
        $collection = $this->configService->all();

        //$namespaces = ComposerConfigHelper::extractPsr4Autoload($collection);
        $namespacesPackages = ComposerConfigHelper::extractPsr4AutoloadPackages($collection);

        $output->writeln('<fg=white>Get packages version...</>');
        $output->writeln('');
        $lastVersions = $this->gitService->lastVersionCollection();

        if ($collection->count() == 0) {
            $output->writeln('<fg=magenta>Not found packages!</>');
            $output->writeln('');
            return 0;
        }
        $deps = [];
        $depsPhp = [];
        foreach ($collection as $configEntity) {
            $output->writeln('<fg=magenta># ' . $configEntity->getId() . '</>');
            $output->writeln('');
            $dep = $this->item($configEntity, $namespacesPackages, $lastVersions);
            $output->writeln(Yaml::dump($dep, 10));
            $deps[] = $dep;
        }
        return 0;
    }

    private function getRequiredFromCoPhpCode(ConfigEntity $configEntity, array $namespacesPackages): array {
        $dir = $configEntity->getPackage()->getDirectory();
        $uses = ComposerConfigHelper::getUses($dir);
        $requirePackage = [];
        if($uses) {
            foreach ($uses as $use) {
                foreach ($namespacesPackages as $namespacesPackage => $packageEntity) {
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

    private function getRequreUpdate(array $lastVersions, array $requires) {
        $requireUpdate = [];
        foreach ($lastVersions as $packageId => $lastVersion) {
            $currentVersion = ArrayHelper::getValue($requires, $packageId);
            if($currentVersion && $lastVersion && version_compare($currentVersion, $lastVersion, '<')) {
                $requireUpdate[$packageId] = $lastVersion;
            }
        }
    }

    private function item(ConfigEntity $configEntity, array $namespacesPackages, array $lastVersions) {
        //$dep['id'] = $configEntity->getId();
        $dep['require'] = $configEntity->getRequire();
        $dep['require-dev'] = $configEntity->getRequireDev();

        $requirePackage = $this->getRequiredFromCoPhpCode($configEntity, $namespacesPackages);

        if(!empty($requirePackage)) {
            //$dep['require-package'] = $requirePackage;
            $wanted = ComposerConfigHelper::getWanted($configEntity, $requirePackage);
            $dep['require-wanted'] = [];
            foreach ($wanted as $packageId) {
                $lastVersion = ArrayHelper::getValue($lastVersions, $packageId);
                $dep['require-wanted'][$packageId] = $lastVersion;
            }
            $requires = $configEntity->getAllRequire();
            if($requires) {
                $dep['require-update'] = $this->getRequreUpdate($lastVersions, $requires);
            }
        }

        //unset($dep['require-package']);
        return $dep;
    }

}
