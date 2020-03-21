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

        //dd($lastVersionCollection);
        //dd($namespacesPackages);
        //dd($namespaces);

        if ($collection->count() == 0) {
            $output->writeln('<fg=magenta>Not found packages!</>');
            $output->writeln('');
            return 0;
        }
        $deps = [];
        $depsPhp = [];
        foreach ($collection as $configEntity) {
            $dep = $this->item($configEntity, $namespacesPackages, $lastVersions);
            $output->writeln('<fg=magenta># ' . $configEntity->getId() . '</>');
            $output->writeln('');
            $output->writeln(Yaml::dump($dep, 10));
            $deps[] = $dep;
        }
        return 0;
    }

    private function item(ConfigEntity $configEntity, array $namespacesPackages, array $lastVersions) {
        //$dep['id'] = $configEntity->getId();
        $dep['require'] = $configEntity->getRequire();
        $dep['require-dev'] = $configEntity->getRequireDev();

        //dd($psr4autoload);

        //$depsPhp[$configEntity->getId()] = ComposerConfigHelper::getUses($configEntity);
        $uses = ComposerConfigHelper::getUses($configEntity);

        $requirePackage = [];
        if($uses) {
            foreach ($uses as $use) {
                foreach ($namespacesPackages as $namespacesPackage => $configEntity) {
                    if (mb_strpos($use, $namespacesPackage) === 0) {
                        $requirePackage[] = $configEntity->getId();
                    }
                }
            }
            $requirePackage = array_unique($requirePackage);
            $requirePackage = array_values($requirePackage);
            $dep['require-package'] = $requirePackage;
        }

        if(!empty($requirePackage)) {
            $wanted = ComposerConfigHelper::getWanted($dep);
            $dep['require-wanted'] = [];
            foreach ($wanted as $itemName) {
                $dep['require-wanted'][$itemName] = ArrayHelper::getValue($lastVersions, $itemName, 'dev-master');
            }
        }

        unset($dep['require-package']);
        return $dep;
    }

}
