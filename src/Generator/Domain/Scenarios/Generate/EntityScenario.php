<?php

namespace PhpLab\Dev\Generator\Domain\Scenarios\Generate;

use PhpLab\Core\Legacy\Code\entities\ClassEntity;
use PhpLab\Core\Legacy\Code\entities\ClassUseEntity;
use PhpLab\Core\Legacy\Code\entities\ClassVariableEntity;
use PhpLab\Core\Legacy\Code\entities\InterfaceEntity;
use PhpLab\Core\Legacy\Code\enums\AccessEnum;
use PhpLab\Core\Legacy\Code\helpers\ClassHelper;
use PhpLab\Core\Legacy\Yii\Helpers\Inflector;
use PhpLab\Dev\Generator\Domain\Dto\BuildDto;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\InterfaceGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\PropertyGenerator;

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

    protected function createClass()
    {
        $className = $this->getClassName();
        $fullClassName = $this->getFullClassName();
        $fileGenerator = new FileGenerator;
        $classGenerator = new ClassGenerator;
        $classGenerator->setName($className);

        $implementedInterfaces = [];
        $fileGenerator->setUse('Symfony\Component\Validator\Constraints', 'Assert');
        $fileGenerator->setUse('PhpLab\Core\Domain\Interfaces\Entity\ValidateEntityInterface');
        $implementedInterfaces[] = 'ValidateEntityInterface';

        if(in_array('id', $this->attributes)) {
            $fileGenerator->setUse('PhpLab\Core\Domain\Interfaces\Entity\EntityIdInterface');
            $implementedInterfaces[] = 'EntityIdInterface';
        }

        $classGenerator->setImplementedInterfaces($implementedInterfaces);

        $validateBody = 'return [];';
        $classGenerator->addMethod('validationRules', [], [], $validateBody);

        if ($this->attributes) {
            foreach ($this->attributes as $attribute) {
                $attributeName = Inflector::variablize($attribute);
                $classGenerator->addProperties([
                    [$attributeName, null, PropertyGenerator::FLAG_PRIVATE]
                ]);
                $setterBody = '$this->' . $attributeName . ' = $value;';
                $classGenerator->addMethod('set' . Inflector::camelize($attributeName), ['value'], [], $setterBody);
                $getterBody = 'return $this->' . $attributeName . ';';
                $classGenerator->addMethod('get' . Inflector::camelize($attributeName), [], [], $getterBody);
            }
        }
        $fileGenerator->setNamespace($this->domainNamespace . '\\' . $this->classDir());
        $fileGenerator->setClass($classGenerator);
        ClassHelper::generateFile($fileGenerator->getNamespace() . '\\' . $className, $fileGenerator->generate());
    }

}
