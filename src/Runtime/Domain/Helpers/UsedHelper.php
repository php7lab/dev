<?php

namespace PhpLab\Dev\Runtime\Domain\Helpers;

use Faker\Provider\File;
use PhpLab\Core\Legacy\Yii\Helpers\FileHelper;

class UsedHelper
{

    public static function register()
    {
        register_shutdown_function(function (){
            $file = __DIR__ . '/../../../../../../../common/runtime/logs/usedClasses/'.time().'.json';
            $classes = get_declared_classes();

            $all = [];
            foreach ($classes as $class) {
                $reflection = new ReflectionClass($class);
                if($reflection->isUserDefined()) {
                    $all['user'][] = $class;
                } else {
                    $all['system'][] = $class;
                }
            }


            $json = json_encode($all, JSON_PRETTY_PRINT);
            FileHelper::save($file, $json);
        });
    }

}