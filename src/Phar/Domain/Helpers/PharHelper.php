<?php

namespace PhpLab\Dev\Phar\Domain\Helpers;

use PhpLab\Core\Legacy\Yii\Helpers\FileHelper;
use PhpLab\Core\Libs\Store\StoreFile;

class PharHelper
{

    public static function loadConfig($profileName): array {
        $config = null;
        if(isset($_ENV['PHAR_CONFIG_FILE']) && file_exists(FileHelper::path($_ENV['PHAR_CONFIG_FILE']))) {
            $store = new StoreFile(FileHelper::path($_ENV['PHAR_CONFIG_FILE']));
            $config = $store->load();
        }
        if(isset($config['profiles'][$profileName])) {
            return $config['profiles'][$profileName];
        }
    }
}