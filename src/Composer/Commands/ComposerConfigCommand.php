<?php

namespace PhpLab\Dev\Composer\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use PhpLab\Core\Console\Widgets\TextWidget;
use PhpLab\Core\Legacy\Yii\Helpers\FileHelper;
use PhpLab\Dev\Package\Domain\Entities\ConfigEntity;
use PhpLab\Dev\Package\Domain\Helpers\ComposerConfigHelper;
use PhpLab\Dev\Composer\Domain\Interfaces\Services\ConfigServiceInterface;
use PhpLab\Dev\Package\Domain\Interfaces\Services\GitServiceInterface;
use PhpLab\Dev\Package\Domain\Libs\Depend;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use yii\helpers\ArrayHelper;
use Symfony\Component\Console\Helper\ProgressBar;

class ComposerConfigCommand extends Command
{

    protected static $defaultName = 'composer:config:dependency-version';

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
        /** @var ConfigEntity[] | Collection $collection */
        $thirdPartyCollection = $this->configService->allWithThirdParty();
        $namespacesPackages = ComposerConfigHelper::extractPsr4AutoloadPackages($thirdPartyCollection);

        $output->writeln('<fg=white>Get packages version...</>');
        $output->writeln('');
        $lastVersions = $this->gitService->lastVersionCollection();

        if ($collection->count() == 0) {
            $output->writeln('<fg=magenta>Not found packages!</>');
            $output->writeln('');
            return 0;
        }

        $output->writeln('<fg=white>Get packages info...</>');
        $output->writeln('');

        $progressBar = new ProgressBar($output);
        $progressBar->setMaxSteps($collection->count());
        $progressBar->start();
        $depend = new Depend($namespacesPackages, $lastVersions);
        $deps = $depend->all($collection, function() use($progressBar) {
            $progressBar->advance();
        });
        $progressBar->finish();

        $output->writeln('');
        $output->writeln('');

        foreach ($deps as $depId => $dep) {
            $output->writeln('<fg=magenta># ' . $depId . '</>');
            $output->writeln('');
            $output->writeln(Yaml::dump($dep, 10));
        }
        return 0;
    }

}
