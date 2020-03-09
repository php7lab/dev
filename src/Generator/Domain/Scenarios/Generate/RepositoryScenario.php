<?php

namespace PhpLab\Dev\Generator\Domain\Scenarios\Generate;

use PhpLab\Core\Domain\Interfaces\Repository\CrudRepositoryInterface;
use PhpLab\Core\Domain\Interfaces\Repository\RepositoryInterface;
use PhpLab\Core\Legacy\Code\entities\ClassEntity;
use PhpLab\Core\Legacy\Code\entities\ClassUseEntity;
use PhpLab\Core\Legacy\Code\entities\ClassVariableEntity;
use PhpLab\Core\Legacy\Code\entities\InterfaceEntity;
use PhpLab\Core\Legacy\Code\helpers\ClassHelper;
use PhpLab\Core\Legacy\Yii\Helpers\Inflector;
use PhpLab\Dev\Generator\Domain\Enums\TypeEnum;
use PhpLab\Dev\Generator\Domain\Helpers\LocationHelper;
use PhpLab\Eloquent\Db\Base\BaseEloquentCrudRepository;
use PhpLab\Eloquent\Db\Base\BaseEloquentRepository;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\InterfaceGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\PropertyGenerator;

class RepositoryScenario extends BaseScenario
{

    public $driver;

    public function typeName()
    {
        return 'Repository';
    }

    public function classDir()
    {
        return 'Repositories';
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
        if ($this->buildDto->isCrudRepository) {
            $fileGenerator->setUse(CrudRepositoryInterface::class);
            $interfaceGenerator->setImplementedInterfaces(['CrudRepositoryInterface']);
        } else {
            $fileGenerator->setUse(RepositoryInterface::class);
            $interfaceGenerator->setImplementedInterfaces(['RepositoryInterface']);
        }
        $fileGenerator->setNamespace($this->domainNamespace . '\\' . $this->interfaceDir());
        $fileGenerator->setClass($interfaceGenerator);
        ClassHelper::generateFile($fileGenerator->getNamespace() . '\\' . $this->getInterfaceName(), $fileGenerator->generate());
    }

    protected function createClass()
    {
        foreach ($this->buildDto->driver as $driver) {
            $this->createOneClass($driver);
        }
    }

    protected function createOneClass(string $driver)
    {
        $className = $this->getClassName();
        $driverDirName = Inflector::camelize($driver);
        $repoClassName = $driverDirName . '\\' . $className;
        $fileGenerator = new FileGenerator;
        $classGenerator = new ClassGenerator;
        $fileGenerator->setNamespace($this->domainNamespace . '\\' . $this->classDir() . '\\' . $driverDirName);

        $parentClass = $this->parentClass($driver);
        if($parentClass) {
            $fileGenerator->setUse($parentClass);
            $classGenerator->setExtendedClass(basename($parentClass));
        }

        $methodGenerator = $this->generateTableNameMethod();
        $classGenerator->addMethodFromGenerator($methodGenerator);

        $entityFullClassName = $this->domainNamespace . LocationHelper::fullClassName($this->name, TypeEnum::ENTITY);
        $entityPureClassName = basename(LocationHelper::fullClassName($this->name, TypeEnum::ENTITY));
        $fileGenerator->setUse($entityFullClassName);

        $methodGenerator = $this->generateGetEntityClassMethod($entityPureClassName);
        $classGenerator->addMethodFromGenerator($methodGenerator);

        $classGenerator->setName($className);
        if ($this->isMakeInterface()) {
            $classGenerator->setImplementedInterfaces([$this->getInterfaceName()]);
            $fileGenerator->setUse($this->getInterfaceFullName());
        }

        $fileGenerator->setClass($classGenerator);
        ClassHelper::generateFile($fileGenerator->getNamespace() . '\\' . $className, $fileGenerator->generate());

    }

    private function generateGetEntityClassMethod(string $entityPureClassName): MethodGenerator {
        $tableName = "{$this->buildDto->domainName}_{$this->buildDto->name}";
        $methodBody = "return {$entityPureClassName}::class;";
        $methodGenerator = new MethodGenerator;
        $methodGenerator->setName('getEntityClass');
        $methodGenerator->setBody($methodBody);
        $methodGenerator->setReturnType('string');
        return $methodGenerator;
    }

    private function generateTableNameMethod(): MethodGenerator {
        $tableName = "{$this->buildDto->domainName}_{$this->buildDto->name}";
        $methodBody = "return '{$tableName}';";
        $methodGenerator = new MethodGenerator;
        $methodGenerator->setName('tableName');
        $methodGenerator->setBody($methodBody);
        $methodGenerator->setReturnType('string');
        return $methodGenerator;
    }

    private function parentClass($driver)
    {
        $className = '';
        if ('eloquent' == $driver) {
            if ($this->buildDto->isCrudRepository) {
                $className = BaseEloquentCrudRepository::class;
            } else {
                $className = BaseEloquentRepository::class;
            }
        } else {
            //$className = 'PhpLab\Core\Domain\Base\BaseRepository';
        }
        return $className;
    }

}
