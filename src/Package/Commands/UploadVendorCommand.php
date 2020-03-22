<?php

namespace PhpLab\Dev\Package\Commands;

use Illuminate\Container\Container;
use PhpLab\Core\Console\Widgets\LogWidget;
use PhpLab\Core\Legacy\Yii\Helpers\ArrayHelper;
use PhpLab\Core\Legacy\Yii\Helpers\FileHelper;
use PhpLab\Dev\Package\Domain\Helpers\Packager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class UploadVendorCommand extends Command
{

    protected static $defaultName = 'package:upload:vendor';

    /*private function start(string $message, OutputInterface $output) {
        $output->write("<fg=white>{$message}... </>");
    }

    private function finishSuccess(OutputInterface $output) {
        $output->writeln("<fg=green>OK</>");
    }

    private function finishFail(string $message, OutputInterface $output) {
        $output->writeln("<fg=red>{$message}</>");
    }*/

    private function ensurePhar(string $pharFile, InputInterface $input, OutputInterface $output) {
        $logWidget = new LogWidget($output);
        $pharGzFile = $pharFile . '.gz';
        if( ! file_exists($pharGzFile)) {
            if( ! file_exists($pharFile)) {
                $packVendorCommand = Container::getInstance()->get(PackVendorCommand::class);
                $packVendorCommand->execute($input, $output);
            }
            $logWidget->start('Compress');
            $content = file_get_contents($pharFile);
            file_put_contents($pharGzFile, gzencode($content));
            $logWidget->finishSuccess();
            /*if(file_exists($pharFile)) {

            } else {
                $logWidget->finishFail('vendor.phar.gz not exists!');
                return 1;
            }*/
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<fg=white># Upload vendor to phar</>');

        $basePath = $_ENV['VENDOR_FTP_DIRECTORY'];
        $pharFile = __DIR__ . '/../../../../../vendor.phar';
        $pharGzFile = $pharFile . '.gz';

        $this->ensurePhar($pharFile, $input, $output);

        $logWidget = new LogWidget($output);

        $logWidget->start('Connect');
        $conn_id = ftp_connect($_ENV['VENDOR_FTP_SERVER']);
        $logWidget->finishSuccess();

        $logWidget->start('Login');
        $login_result = ftp_login($conn_id, $_ENV['VENDOR_FTP_USERNAME'], $_ENV['VENDOR_FTP_PASSWORD']);
        $logWidget->finishSuccess();

        $logWidget->start('Get version list');
        $versionList = ftp_nlist($conn_id, $basePath);
        $logWidget->finishSuccess();

        ArrayHelper::removeByValue('.', $versionList);
        ArrayHelper::removeByValue('..', $versionList);
        $versionList = array_map(function($value) {
            return trim($value, 'v');
        }, $versionList);
        natsort($versionList);
        array_splice($versionList, 0, -3);
        $output->writeln('<fg=white>'.implode(PHP_EOL, $versionList).'</>');

        $logWidget->start('Check last version');
        $lastSize = ftp_size($conn_id, $basePath . '/' . ArrayHelper::last($versionList) . '/vendor.phar.gz');
        //exit("$lastSize == ".filesize($pharGzFile));
        if($lastSize == filesize($pharGzFile)) {
            $logWidget->finishSuccess('Already published!');
            return 0;
        }
        $logWidget->finishSuccess();

        $helper = $this->getHelper('question');
        $question = new Question('Enter new version: ');
        $version = $helper->ask($input, $output, $question);
        $version = trim($version, 'v');
        if( ! preg_match('#\d+\.\d+\.\d+#', $version)) {
            $output->writeln('<fg=red>bad version!</>');
            return 1;
        }

        if(in_array($version, $versionList)) {
            $output->writeln('<fg=red>exists version!</>');
            return 1;
        }
        $remoteDirectory = $basePath . '/' . $version;
        $remoteFile = $remoteDirectory . '/' . basename($pharGzFile);

        $logWidget->start('Make directory');
        ftp_mkdir($conn_id, $remoteDirectory);
        $logWidget->finishSuccess();

        $logWidget->start('Uploading');
        if (ftp_put($conn_id, $remoteFile, $pharGzFile, FTP_BINARY)) {
            $logWidget->finishSuccess();
        } else {
            $logWidget->finishFail();
            $logWidget->start('Remove directory');
            ftp_delete($remoteDirectory);
            $logWidget->finishSuccess();
        }

        $logWidget->start('Close connect');
        ftp_close($conn_id);
        $logWidget->finishSuccess();

        return 0;
    }

}
