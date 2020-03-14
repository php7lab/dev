<?php

namespace PhpLab\Dev\Package\Commands;

use Illuminate\Support\Collection;
use PhpLab\Core\Legacy\Yii\Helpers\FileHelper;
use PhpLab\Dev\Package\Domain\Entities\PackageEntity;
use PhpLab\Dev\Package\Domain\Helpers\Packager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class PackVendorCommand extends Command
{

    protected static $defaultName = 'package:pack';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<fg=white># Pack vendor to phar</>');
        $rootDir = FileHelper::rootPath();
        $packager = new Packager($rootDir . '/vendor');
        $packager->export($rootDir);
    }

}
