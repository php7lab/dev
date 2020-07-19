<?php


use Illuminate\Container\Container;
use Symfony\Component\Console\Application;
use PhpLab\Core\Console\Helpers\CommandHelper;

/**
 * @var Application $application
 * @var Container $container
 */

$container = Container::getInstance();

// --- Application ---

$container->bind(Application::class, Application::class, true);

// --- Generator ---

$container->bind(\PhpLab\Dev\Generator\Domain\Interfaces\Services\DomainServiceInterface::class, \PhpLab\Dev\Generator\Domain\Services\DomainService::class);
$container->bind(\PhpLab\Dev\Generator\Domain\Interfaces\Services\ModuleServiceInterface::class, \PhpLab\Dev\Generator\Domain\Services\ModuleService::class);

// --- Composer ---


$container->bind(\PhpLab\Dev\Composer\Domain\Interfaces\Repositories\ConfigRepositoryInterface::class, \PhpLab\Dev\Composer\Domain\Repositories\File\ConfigRepository::class);
$container->bind(\PhpLab\Dev\Composer\Domain\Interfaces\Services\ConfigServiceInterface::class, \PhpLab\Dev\Composer\Domain\Services\ConfigService::class);

// --- Package ---

$container->bind(\PhpLab\Dev\Package\Domain\Interfaces\Services\GitServiceInterface::class, \PhpLab\Dev\Package\Domain\Services\GitService::class);
$container->bind(\PhpLab\Dev\Package\Domain\Interfaces\Services\PackageServiceInterface::class, \PhpLab\Dev\Package\Domain\Services\PackageService::class);
$container->bind(\PhpLab\Dev\Package\Domain\Repositories\File\GroupRepository::class, function () {
    $fileName = ! empty($_ENV['PACKAGE_GROUP_CONFIG']) ? __DIR__ . '/../../../../' . $_ENV['PACKAGE_GROUP_CONFIG'] : __DIR__ . '/../src/Package/Domain/Data/package_group.php';
    $repo = new \PhpLab\Dev\Package\Domain\Repositories\File\GroupRepository($fileName);
    return $repo;
});
$container->bind(\PhpLab\Dev\Package\Domain\Interfaces\Repositories\PackageRepositoryInterface::class, \PhpLab\Dev\Package\Domain\Repositories\File\PackageRepository::class);
$container->bind(\PhpLab\Dev\Package\Domain\Interfaces\Repositories\GitRepositoryInterface::class, \PhpLab\Dev\Package\Domain\Repositories\File\GitRepository::class);

CommandHelper::registerFromNamespaceList([
    'PhpLab\Dev\Generator\Commands',
    'PhpLab\Dev\Stress\Commands',
    'PhpLab\Dev\Package\Commands',
    'PhpLab\Dev\Composer\Commands',
    'PhpLab\Dev\Phar\Commands',
], $container);
