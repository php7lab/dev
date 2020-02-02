<?php

namespace PhpLab\Dev\Generator\Domain\Scenarios\Generate;

class EntityScenario extends BaseScenario
{

    public function init()
    {
        $this->attributes = $this->buildDto->attributes;
    }

    public function typeName()
    {
        return 'Entity';
    }

    public function classDir()
    {
        return 'Entities';
    }

}
