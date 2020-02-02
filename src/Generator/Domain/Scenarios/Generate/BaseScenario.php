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

abstract class BaseScenario
{

    public $domainNamespace;
    public $name;
    public $attributes;

    /** @var BuildDto */
    public $buildDto;

    abstract public function typeName();

    abstract public function classDir();

    public function init()
    {

    }

    public function run()
    {
        if ($this->isMakeInterface()) {
            $this->createInterface();
        }
        $this->createClass();
    }

    protected function isMakeInterface(): bool
    {
        return false;
    }

    protected function getClassName(): string
    {
        return Inflector::classify($this->buildDto->name) . $this->typeName();
    }

    protected function getFullClassName(): string
    {
        return $this->domainNamespace . '\\' . $this->classDir() . '\\' . $this->getClassName();
    }

    protected function interfaceDir()
    {
        return 'Interfaces\\' . $this->classDir();
    }

    protected function getInterfaceFullName(): string
    {
        return $this->domainNamespace . '\\' . $this->interfaceDir() . '\\' . $this->getInterfaceName();
    }

    protected function getInterfaceName(): string
    {
        $className = $this->getClassName();
        return $className . 'Interface';
    }

    protected function createInterface()
    {
        $fileGenerator = new FileGenerator;
        $interfaceGenerator = new InterfaceGenerator;
        $interfaceGenerator->setName($this->getInterfaceName());

        $fileGenerator->setClass($interfaceGenerator);
        $fileGenerator->setUse($this->getInterfaceFullName());
        $fileGenerator->setNamespace($this->domainNamespace . '\\' . $this->interfaceDir());
        ClassHelper::generateFile($this->getInterfaceName(), $fileGenerator->generate());

        /*$className = $this->getClassName();
        $interfaceEntity = new InterfaceEntity;
        $interfaceEntity->name = $this->getInterfaceFullName($className);
        ClassHelper::generate($interfaceEntity);
        return $interfaceEntity;*/
    }

    protected function createClass()
    {
        $className = $this->getClassName();
        $fullClassName = $this->getFullClassName();
        $fileGenerator = new FileGenerator;
        $classGenerator = new ClassGenerator;
        $classGenerator->setName($className);
        if ($this->isMakeInterface()) {
            $classGenerator->setImplementedInterfaces([$this->getInterfaceName()]);
            $fileGenerator->setUse($this->getInterfaceFullName());
        }
        if ($this->attributes) {
            foreach ($this->attributes as $attribute) {
                $classGenerator->addProperties([
                    [Inflector::variablize($attribute)]
                ]);
            }
        }
        $fileGenerator->setNamespace($this->domainNamespace . '\\' . $this->classDir());
        $fileGenerator->setClass($classGenerator);
        ClassHelper::generateFile($fileGenerator->getNamespace() . '\\' . $className, $fileGenerator->generate());


        /*$classGenerator->addMethods([
            MethodGenerator::fromArray([
                'name' => 'getName',
                'body' => "return '{$this->buildDto->domainName}';",
            ]),
        ]);*/


        /*$className = $this->getClassName();
        $uses = [];
        $classEntity = new ClassEntity;
        $classEntity->name = $this->domainNamespace . '\\' . $this->classDir() . '\\' . $className;
        if($this->isMakeInterface()) {
            $useEntity = new ClassUseEntity;
            $useEntity->name = $this->getInterfaceFullName();
            $uses[] = $useEntity;
            $classEntity->implements = $this->getInterfaceName();
        }

        if($this->attributes) {
            foreach ($this->attributes as $attribute) {
                $variableEntity = new ClassVariableEntity;
                $variableEntity->name = Inflector::variablize($attribute);
                //$variableEntity->access = AccessEnum::PRIVATE;
                $classEntity->addVariable($variableEntity);
            }
        }
        ClassHelper::generate($classEntity, $uses);
        return $classEntity;*/
    }
}
