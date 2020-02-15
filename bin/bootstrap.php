<?php

use Symfony\Component\Console\Application;

/**
 * @var Application $application
 */

// --- Generator ---

use PhpLab\Dev\Generator\Commands\DomainCommand;
use PhpLab\Dev\Generator\Commands\ModuleCommand;
use PhpLab\Dev\Generator\Domain\Services\DomainService;
use PhpLab\Dev\Generator\Domain\Services\ModuleService;

// создаем и объявляем команды
$domainService = new DomainService;
$moduleService = new ModuleService;

$command = new DomainCommand(null, $domainService);
$application->add($command);

$command = new ModuleCommand(null, $moduleService);
$application->add($command);

// --- Stress ---

use PhpLab\Dev\Stress\Commands\StressCommand;
use PhpLab\Dev\Stress\Domain\Services\StressService;

// создаем и объявляем команды
$stressService = new StressService;
$command = new StressCommand(null, $stressService);
$application->add($command);

// --- Package ---

use PhpLab\Dev\Package\Commands\GitChangedCommand;
use PhpLab\Dev\Package\Commands\GitNeedReleaseCommand;
use PhpLab\Dev\Package\Commands\GitPullCommand;
use PhpLab\Dev\Package\Commands\GitVersionCommand;
use PhpLab\Dev\Package\Domain\Repositories\File\GitRepository;
use PhpLab\Dev\Package\Domain\Repositories\File\GroupRepository;
use PhpLab\Dev\Package\Domain\Repositories\File\PackageRepository;
use PhpLab\Dev\Package\Domain\Services\GitService;
use PhpLab\Dev\Package\Domain\Services\PackageService;

$fileName = !empty($_ENV['PACKAGE_GROUP_CONFIG']) ? __DIR__ . '/../../../../' . $_ENV['PACKAGE_GROUP_CONFIG'] : __DIR__ . '/../src/Package/Domain/Data/package_group.php';
$groupRepository = new GroupRepository($fileName);
$packageRepository = new PackageRepository($groupRepository);
$gitRepository = new GitRepository($packageRepository);

$packageService = new PackageService($packageRepository);
$gitService = new GitService($gitRepository);

$command = new GitPullCommand(null, $packageService, $gitService);
$application->add($command);

$command = new GitChangedCommand(null, $packageService, $gitService);
$application->add($command);

$command = new GitNeedReleaseCommand(null, $packageService, $gitService);
$application->add($command);

$command = new GitVersionCommand(null, $packageService, $gitService);
$application->add($command);
