<?php

namespace PhpLab\Dev\Generator\Domain\Services;

use PhpLab\Core\Legacy\Yii\Helpers\Inflector;
use PhpLab\Core\Helpers\ClassHelper;
use PhpLab\Dev\Generator\Domain\Dto\BuildDto;
use PhpLab\Dev\Generator\Domain\Interfaces\Services\DomainServiceInterface;
use PhpLab\Dev\Generator\Domain\Scenarios\Generate\BaseScenario;

class DomainService implements DomainServiceInterface
{

    public function generate(BuildDto $buildDto)
    {
        foreach ($buildDto->types as $typeName) {
            $type = $typeName;
            $type = Inflector::classify($type);
            $scenarioInstance = $this->createScenarioByTypeName($type);
            $scenarioParams = [
                'name' => $buildDto->name,
                'driver' => $buildDto->driver,
                'buildDto' => $buildDto,
                'domainNamespace' => $buildDto->domainNamespace,
            ];
            ClassHelper::configure($scenarioInstance, $scenarioParams);
            $scenarioInstance->init();
            $scenarioInstance->run();
        }
    }

    private function createScenarioByTypeName($type): BaseScenario
    {
        $scenarioClass = 'PhpLab\\Dev\\Generator\\Domain\Scenarios\\Generate\\' . $type . 'Scenario';
        /** @var BaseScenario $scenarioInstance */
        $scenarioInstance = new $scenarioClass;
        return $scenarioInstance;
    }

}
