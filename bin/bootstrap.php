<?php

use PhpLab\Dev\Generator\Commands\DomainCommand;
use PhpLab\Dev\Generator\Commands\ModuleCommand;
use PhpLab\Dev\Generator\Domain\Services\DomainService;
use PhpLab\Dev\Generator\Domain\Services\ModuleService;
use PhpLab\Dev\Package\Commands\ComposerConfigCommand;
use PhpLab\Dev\Package\Commands\GitChangedCommand;
use PhpLab\Dev\Package\Commands\GitNeedReleaseCommand;
use PhpLab\Dev\Package\Commands\GitPullCommand;
use PhpLab\Dev\Package\Commands\GitVersionCommand;
use PhpLab\Dev\Package\Domain\Repositories\File\ConfigRepository;
use PhpLab\Dev\Package\Domain\Repositories\File\GitRepository;
use PhpLab\Dev\Package\Domain\Repositories\File\GroupRepository;
use PhpLab\Dev\Package\Domain\Repositories\File\PackageRepository;
use PhpLab\Dev\Package\Domain\Services\ConfigService;
use PhpLab\Dev\Package\Domain\Services\GitService;
use PhpLab\Dev\Package\Domain\Services\PackageService;
use PhpLab\Dev\Stress\Commands\StressCommand;
use Symfony\Component\Console\Application;

/**
 * @var Application $application
 */

$container = \Illuminate\Container\Container::getInstance();

// --- Generator ---

$container->bind(\PhpLab\Dev\Generator\Domain\Interfaces\Services\DomainServiceInterface::class, DomainService::class);
$container->bind(\PhpLab\Dev\Generator\Domain\Interfaces\Services\ModuleServiceInterface::class, ModuleService::class);

// --- Package ---

$container->bind(\PhpLab\Dev\Package\Domain\Interfaces\Services\PackageServiceInterface::class, PackageService::class);
$container->bind(\PhpLab\Dev\Package\Domain\Interfaces\Services\GitServiceInterface::class, GitService::class);
$container->bind(\PhpLab\Dev\Package\Domain\Interfaces\Services\ConfigServiceInterface::class, ConfigService::class);
$container->bind(GroupRepository::class, function () {
    $fileName = ! empty($_ENV['PACKAGE_GROUP_CONFIG']) ? __DIR__ . '/../../../../' . $_ENV['PACKAGE_GROUP_CONFIG'] : __DIR__ . '/../src/Package/Domain/Data/package_group.php';
    $repo = new GroupRepository($fileName);
    return $repo;
});
$container->bind(\PhpLab\Dev\Package\Domain\Interfaces\Repositories\ConfigRepositoryInterface::class, ConfigRepository::class);
$container->bind(\PhpLab\Dev\Package\Domain\Interfaces\Repositories\PackageRepositoryInterface::class, PackageRepository::class);
$container->bind(\PhpLab\Dev\Package\Domain\Interfaces\Repositories\GitRepositoryInterface::class, GitRepository::class);

// --- Generator ---

$command = $container->get(DomainCommand::class);
$application->add($command);

$command = $container->get(ModuleCommand::class);
$application->add($command);

// --- Stress ---

$command = $container->get(StressCommand::class);
$application->add($command);

// --- Package ---

$command = $container->get(GitPullCommand::class);
$application->add($command);

$command = $container->get(GitChangedCommand::class);
$application->add($command);

$command = $container->get(GitNeedReleaseCommand::class);
$application->add($command);

$command = $container->get(GitVersionCommand::class);
$application->add($command);

$command = $container->get(ComposerConfigCommand::class);
$application->add($command);

$command = $container->get(\PhpLab\Dev\Package\Commands\ComposerWandedVersionCommand::class);
$application->add($command);

$command = $container->get(\PhpLab\Dev\Package\Commands\PackVendorCommand::class);
$application->add($command);
