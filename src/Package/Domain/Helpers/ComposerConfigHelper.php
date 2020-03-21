<?php

namespace PhpLab\Dev\Package\Domain\Helpers;

use Illuminate\Support\Collection;
use PhpLab\Core\Legacy\Yii\Helpers\ArrayHelper;
use PhpLab\Core\Legacy\Yii\Helpers\FileHelper;
use PhpLab\Dev\Package\Domain\Entities\ConfigEntity;

class ComposerConfigHelper
{

    public static function getWanted(array $dep)
    {
        $wanted = [];
        if(!empty($dep['require-package'])) {
            foreach ($dep['require-package'] as $packageId) {
                $isDeclared = isset($dep['require'][$packageId]) || isset($dep['require-dev'][$packageId]);
                if( ! $isDeclared) {
                    $wanted[] = $packageId;
                }
            }
            $wanted = array_unique($wanted);
            $wanted = array_values($wanted);
        }
        return $wanted;
    }

    public static function getUses(ConfigEntity $configEntity)
    {
        $dir = $configEntity->getPackage()->getDirectory();
        $options['only'][] = '*.php';
        $phpScripts = FileHelper::findFiles($dir, $options);
        $depss = [];
        foreach ($phpScripts as $phpScriptFileName) {
            $code = FileHelper::load($phpScriptFileName);
            preg_match_all('#use\s+(.+);#iu', $code, $matches);
            if(!empty($matches[1])) {
                $depss = array_merge($depss, $matches[1]);
            }
        }
        $depss = array_unique($depss);
        $depss = array_values($depss);
        return $depss;
    }

    public static function extractPsr4Autoload(Collection $collection)
    {
        /** @var ConfigEntity[] | Collection $collection */
        $namespaces = [];
        foreach ($collection as $configEntity) {
            $psr4autoloads = self::extractPsr4($configEntity);
            if($psr4autoloads) {
                foreach ($psr4autoloads as $autoloadNamespace => $path) {
                    $autoloadNamespace = trim($autoloadNamespace, '\\');
                    $path = 'vendor' . DIRECTORY_SEPARATOR . $configEntity->getPackage()->getId() . DIRECTORY_SEPARATOR . $path;
                    $path = str_replace('\\', '/', $path);
                    $path = trim($path, '/');
                    //$path = realpath($path);
                    $namespaces[$autoloadNamespace] = $path;
                }
            }
        }
        return $namespaces;
    }

    public static function extractPsr4AutoloadPackages(Collection $collection)
    {
        /** @var ConfigEntity[] | Collection $collection */
        $namespaces = [];
        foreach ($collection as $configEntity) {
            $psr4autoloads = self::extractPsr4($configEntity);
            if($psr4autoloads) {
                foreach ($psr4autoloads as $autoloadNamespace => $path) {
                    $autoloadNamespace = trim($autoloadNamespace, '\\');
                    $namespaces[$autoloadNamespace] = $configEntity->getPackage();
                }
            }
        }
        return $namespaces;
    }

    public static function extractPsr4(ConfigEntity $configEntity)
    {
        $psr4autoloads = $configEntity->getAutoloadItem('psr-4');
        $psr4devAutoloads = $configEntity->getAutoloadItem('psr-4');
        return array_merge($psr4autoloads, $psr4devAutoloads);
    }

}
