<?php

use Illuminate\Container\Container;
use Symfony\Component\Console\Application;

/**
 * @var Application $application
 * @var Container $container
 */

// --- Generator ---

$container->bind(\PhpLab\Dev\Generator\Domain\Interfaces\Services\DomainServiceInterface::class, \PhpLab\Dev\Generator\Domain\Services\DomainService::class);
$container->bind(\PhpLab\Dev\Generator\Domain\Interfaces\Services\ModuleServiceInterface::class, \PhpLab\Dev\Generator\Domain\Services\ModuleService::class);

// --- Package ---

$container->bind(\PhpLab\Dev\Package\Domain\Interfaces\Services\PackageServiceInterface::class, \PhpLab\Dev\Package\Domain\Services\PackageService::class);
$container->bind(\PhpLab\Dev\Package\Domain\Interfaces\Services\GitServiceInterface::class, \PhpLab\Dev\Package\Domain\Services\GitService::class);
$container->bind(\PhpLab\Dev\Package\Domain\Interfaces\Services\ConfigServiceInterface::class, \PhpLab\Dev\Package\Domain\Services\ConfigService::class);
$container->bind(\PhpLab\Dev\Package\Domain\Repositories\File\GroupRepository::class, function () {
    $fileName = ! empty($_ENV['PACKAGE_GROUP_CONFIG']) ? __DIR__ . '/../../../../' . $_ENV['PACKAGE_GROUP_CONFIG'] : __DIR__ . '/../src/Package/Domain/Data/package_group.php';
    $repo = new \PhpLab\Dev\Package\Domain\Repositories\File\GroupRepository($fileName);
    return $repo;
});
$container->bind(\PhpLab\Dev\Package\Domain\Interfaces\Repositories\ConfigRepositoryInterface::class, \PhpLab\Dev\Package\Domain\Repositories\File\ConfigRepository::class);
$container->bind(\PhpLab\Dev\Package\Domain\Interfaces\Repositories\PackageRepositoryInterface::class, \PhpLab\Dev\Package\Domain\Repositories\File\PackageRepository::class);
$container->bind(\PhpLab\Dev\Package\Domain\Interfaces\Repositories\GitRepositoryInterface::class, \PhpLab\Dev\Package\Domain\Repositories\File\GitRepository::class);

// --- Generator ---

$command = $container->get(\PhpLab\Dev\Generator\Commands\DomainCommand::class);
$application->add($command);

$command = $container->get(\PhpLab\Dev\Generator\Commands\ModuleCommand::class);
$application->add($command);

// --- Stress ---

$command = $container->get(\PhpLab\Dev\Stress\Commands\StressCommand::class);
$application->add($command);

// --- Git ---

$command = $container->get(\PhpLab\Dev\Package\Commands\GitPullCommand::class);
$application->add($command);

$command = $container->get(\PhpLab\Dev\Package\Commands\GitChangedCommand::class);
$application->add($command);

$command = $container->get(\PhpLab\Dev\Package\Commands\GitNeedReleaseCommand::class);
$application->add($command);

$command = $container->get(\PhpLab\Dev\Package\Commands\GitVersionCommand::class);
$application->add($command);

// --- Composer ---

$command = $container->get(\PhpLab\Dev\Package\Commands\ComposerConfigCommand::class);
$application->add($command);

$command = $container->get(\PhpLab\Dev\Package\Commands\ComposerWandedVersionCommand::class);
$application->add($command);

// --- phar ---

$command = $container->get(\PhpLab\Dev\Phar\Commands\PackVendorCommand::class);
$application->add($command);

$command = $container->get(\PhpLab\Dev\Phar\Commands\PackApplicationCommand::class);
$application->add($command);

$command = $container->get(\PhpLab\Dev\Phar\Commands\UploadVendorCommand::class);
$application->add($command);
