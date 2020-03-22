<?php

namespace PhpLab\Dev\Package\Commands;

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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<fg=white># Upload vendor to phar</>');

        $basePath = $_ENV['VENDOR_FTP_DIRECTORY'];
        $localFile = __DIR__ . '/../../../../../vendor.phar.gz';
        $localFileOriginal = __DIR__ . '/../../../../../vendor.phar';

        if( ! file_exists($localFile)) {
            if(file_exists($localFileOriginal)) {
                $output->writeln('<fg=white>Compress...</>');
                $content = file_get_contents($localFileOriginal);
                file_put_contents($localFile, gzencode($content));
            } else {
                $output->writeln('<fg=red>vendor.phar.gz not exists!</>');
                return 1;
            }
        }

        $output->writeln('<fg=white>Connect...</>');
        $conn_id = ftp_connect($_ENV['VENDOR_FTP_SERVER']);
        $output->writeln('<fg=white>Login...</>');
        $login_result = ftp_login($conn_id, $_ENV['VENDOR_FTP_USERNAME'], $_ENV['VENDOR_FTP_PASSWORD']);

        $output->writeln('<fg=white>Get version list...</>');
        $versionList = ftp_nlist($conn_id, $basePath);
        ArrayHelper::removeByValue('.', $versionList);
        ArrayHelper::removeByValue('..', $versionList);
        $versionList = array_map(function($value) {
            return trim($value, 'v');
        }, $versionList);
        natsort($versionList);
        array_splice($versionList, 0, -3);
        $output->writeln('<fg=white>'.implode(PHP_EOL, $versionList).'</>');

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
        $remoteFile = $remoteDirectory . '/' . basename($localFile);
        $output->writeln('<fg=white>Make directory...</>');
        ftp_mkdir($conn_id, $remoteDirectory);
        $output->writeln('<fg=white>Uploading...</>');
        if (ftp_put($conn_id, $remoteFile, $localFile, FTP_BINARY)) {
            $output->writeln('<fg=green>successfully uploaded!</>');
        } else {
            $output->writeln('<fg=white>Remove directory...</>');
            ftp_delete($remoteDirectory);
            $output->writeln('<fg=red>There was a problem while uploading!</>');
        }
        $output->writeln('<fg=white>Close connect...</>');
        ftp_close($conn_id);
        return 0;
    }

}
