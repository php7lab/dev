<?php

namespace PhpLab\Dev\Generator\Domain\Services;

use PhpLab\Core\Legacy\Yii\Helpers\Inflector;
use PhpLab\Core\Helpers\ClassHelper;
use PhpLab\Dev\Generator\Domain\Dto\BuildDto;
use PhpLab\Dev\Generator\Domain\Interfaces\Services\ModuleServiceInterface;
use PhpLab\Dev\Generator\Domain\Scenarios\Generate\BaseScenario;

class ModuleService implements ModuleServiceInterface
{

    public function generate(BuildDto $buildDto)
    {
        $type = Inflector::classify($buildDto->typeModule);
        $scenarioInstance = $this->createScenarioByTypeName($type);
        $scenarioParams = [
            'buildDto' => $buildDto,
            'moduleNamespace' => $buildDto->moduleNamespace,
        ];
        ClassHelper::configure($scenarioInstance, $scenarioParams);
        $scenarioInstance->init();
        $scenarioInstance->run();
    }

    private function createScenarioByTypeName($type): BaseScenario
    {
        $scenarioClass = 'PhpLab\\Dev\\Generator\\Domain\Scenarios\\Generate\\' . $type . 'Scenario';
        /** @var BaseScenario $scenarioInstance */
        $scenarioInstance = new $scenarioClass;
        return $scenarioInstance;
    }

}
