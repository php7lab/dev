<?php

namespace PhpLab\Dev\Package\Commands;

use PhpLab\Core\Console\Widgets\LogWidget;
use PhpLab\Core\Legacy\Yii\Helpers\FileHelper;
use PhpLab\Dev\Package\Domain\Helpers\Packager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PackVendorCommand extends Command
{

    protected static $defaultName = 'package:pack:vendor';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<fg=white># Pack vendor to phar</>');

        $logWidget = new LogWidget($output);

        $logWidget->start('Pack files');
        $rootDir = FileHelper::rootPath();
        $packager = new Packager;
        $packager->exportVendor($rootDir . '/vendor', $this->excludes());
        $logWidget->finishSuccess();

        return 0;
    }

    private function excludes()
    {
        $config = null;
        if(isset($_ENV['PHAR_CONFIG_FILE']) && file_exists(FileHelper::path($_ENV['PHAR_CONFIG_FILE']))) {
            $config = include FileHelper::path($_ENV['PHAR_CONFIG_FILE']);
        }
        if($config['excludes']) {
            return $config['excludes'];
        }

        return [
            'regex:#\/(|tests|test|docs|doc|examples|example|benchmarks|benchmark|\.git)\/#iu',
            '/composer.json',
            '/composer.lock',
            '/LICENSE',
            '/CHANGELOG',
            '/AUTHORS',
            '/Makefile',
            '/Vagrantfile',
            '/phpbench.json',
            '/appveyor.yml',
            '/phpstan.',
            '/phpunit.xml',
            //'/amphp/http-client-cookies/res/',
            //'/zendframework/',
            '/tivie/',
            '/nesbot/',
            '/kelunik/',
            //'/league/',
            //'/symfony/translation/',
            //'/symfony/translation-contracts/',
            //'/symfony/service-contracts/',
            '/php7lab/dev/',
            '/php7lab/test/',
            '/zndoc/rest-api/',
            //'/symfony/web-server-bundle',
            '/phpunit/',
            //'/codeception/',
            'regex:#[\s\S]+\.(md|bat|dist|rar|zip|gz|phar|py|sh|bat|cmd|exe|h|c)#iu',
        ];
    }
}
