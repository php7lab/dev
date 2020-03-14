<?php

namespace PhpLab\Dev\Package\Commands;

use PhpLab\Core\Legacy\Yii\Helpers\FileHelper;
use PhpLab\Dev\Package\Domain\Helpers\Packager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PackVendorCommand extends Command
{

    protected static $defaultName = 'package:pack';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<fg=white># Pack vendor to phar</>');
        $rootDir = FileHelper::rootPath();
        $packager = new Packager($rootDir . '/vendor', $this->excludes());
        $packager->export($rootDir);
    }

    private function excludes()
    {
        return [
            //'/tests',
            'regex:#\/(docs|doc|examples|example)\/#iu',
            '/composer.json',
            '/LICENSE',
            '/phpunit.xml',
            '/php7lab/dev',
            '/php7lab/test',
            '/zndoc/rest-api',
            //'/symfony/web-server-bundle',
            '/phpunit/',
            //'/codeception/',
            'regex:#[\s\S]+\.(md|bat|dist)#iu',
        ];
    }
}
