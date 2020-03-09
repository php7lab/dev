<?php

namespace PhpLab\Dev\Generator\Domain\Scenarios\Generate;

use PhpLab\Core\Domain\Interfaces\Service\CrudServiceInterface;
use PhpLab\Core\Legacy\Code\entities\ClassEntity;
use PhpLab\Core\Legacy\Code\entities\ClassUseEntity;
use PhpLab\Core\Legacy\Code\entities\ClassVariableEntity;
use PhpLab\Core\Legacy\Code\entities\DocBlockEntity;
use PhpLab\Core\Legacy\Code\entities\DocBlockParameterEntity;
use PhpLab\Core\Legacy\Code\entities\InterfaceEntity;
use PhpLab\Core\Legacy\Code\enums\AccessEnum;
use PhpLab\Core\Legacy\Code\helpers\ClassHelper;
use PhpLab\Core\Legacy\Yii\Helpers\Inflector;
use PhpLab\Dev\Generator\Domain\Enums\TypeEnum;
use PhpLab\Dev\Generator\Domain\Helpers\LocationHelper;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\InterfaceGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;

class ServiceScenario extends BaseScenario
{

    public function typeName()
    {
        return 'Service';
    }

    public function classDir()
    {
        return 'Services';
    }

    protected function isMakeInterface(): bool
    {
        return true;
    }

    protected function createInterface()
    {
        $fileGenerator = new FileGenerator;
        $interfaceGenerator = new InterfaceGenerator;
        $interfaceGenerator->setName($this->getInterfaceName());
        if ($this->buildDto->isCrudService) {
            $fileGenerator->setUse(CrudServiceInterface::class);
            $interfaceGenerator->setImplementedInterfaces(['CrudServiceInterface']);
        }
        $fileGenerator->setNamespace($this->domainNamespace . '\\' . $this->interfaceDir());
        $fileGenerator->setClass($interfaceGenerator);
        ClassHelper::generateFile($fileGenerator->getNamespace() . '\\' . $this->getInterfaceName(), $fileGenerator->generate());


        /*$className = $this->getClassName();
        $uses = [];
        $interfaceEntity = new InterfaceEntity;
        $interfaceEntity->name = $this->getInterfaceFullName($className);
        if($this->buildDto->isCrudService) {
            $uses[] = new ClassUseEntity(['name' => 'PhpLab\Core\Domain\Interfaces\Service\CrudServiceInterface']);
            $interfaceEntity->extends = 'CrudServiceInterface';
        }
        ClassHelper::generate($interfaceEntity, $uses);
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

        $repositoryInterfaceFullClassName = $this->buildDto->domainNamespace . LocationHelper::fullInterfaceName($this->name, TypeEnum::REPOSITORY);
        $repositoryInterfacePureClassName = basename($repositoryInterfaceFullClassName);
        $fileGenerator->setUse($repositoryInterfaceFullClassName);
        //$repositoryInterfaceClassName = basename($repositoryInterfaceFullClassName);
        //$fileGenerator->setUse($repositoryInterfaceFullClassName);

        if ($this->attributes) {
            foreach ($this->attributes as $attribute) {
                $classGenerator->addProperties([
                    [Inflector::variablize($attribute)]
                ]);
            }
        }
        $fileGenerator->setNamespace($this->domainNamespace . '\\' . $this->classDir());
        $fileGenerator->setClass($classGenerator);


        if ($this->buildDto->isCrudService) {
            $fileGenerator->setUse('PhpLab\Core\Domain\Base\BaseCrudService');
            $classGenerator->setExtendedClass('BaseCrudService');
        } else {
            $fileGenerator->setUse('PhpLab\Core\Domain\Base\BaseService');
            $classGenerator->setExtendedClass('BaseService');
        }

        $parameterGenerator = new ParameterGenerator;
        $parameterGenerator->setName('repository');
        $parameterGenerator->setType($repositoryInterfacePureClassName);

        $methodGenerator = new MethodGenerator;
        $methodGenerator->setName('__construct');
        $methodGenerator->setParameter($parameterGenerator);
        $methodGenerator->setBody('$this->repository = $repository;');

        $classGenerator->addMethods([$methodGenerator]);

        /*$code = "
    public function __construct({$repositoryInterfaceClassName} \$repository)
    {
        \$this->repository = \$repository;
    }
";*/

        $phpCode = $fileGenerator->generate();
        $phpCode = str_replace('public function __construct(\\', 'public function __construct(', $phpCode);

        ClassHelper::generateFile($fileGenerator->getNamespace() . '\\' . $className, $phpCode);


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

        $repositoryInterfaceFullClassName = $this->buildDto->domainNamespace . LocationHelper::fullInterfaceName($this->name, TypeEnum::REPOSITORY);
        $repositoryInterfaceClassName = basename($repositoryInterfaceFullClassName);
        $uses[] = new ClassUseEntity(['name' => $repositoryInterfaceFullClassName]);

        if($this->buildDto->isCrudService) {
            $uses[] = new ClassUseEntity(['name' => 'PhpLab\Core\Domain\Base\BaseCrudService']);
            $classEntity->extends = 'BaseCrudService';
        } else {
            $uses[] = new ClassUseEntity(['name' => 'PhpLab\Core\Domain\Base\BaseService']);
            $classEntity->extends = 'BaseService';
        }

        $classEntity->code = "
    public function __construct({$repositoryInterfaceClassName} \$repository)
    {
        \$this->repository = \$repository;
    }
";

        ClassHelper::generate($classEntity, $uses);
        return $classEntity;*/
    }
}
