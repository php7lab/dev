<?php

namespace PhpLab\Dev\Generator\Domain\Scenarios\Generate;

use PhpLab\Core\Domain\Traits\ForgeEntityTrait;
use PhpLab\Core\Legacy\Code\entities\ClassEntity;
use PhpLab\Core\Legacy\Code\entities\ClassUseEntity;
use PhpLab\Core\Legacy\Code\entities\ClassVariableEntity;
use PhpLab\Core\Legacy\Code\entities\InterfaceEntity;
use PhpLab\Core\Legacy\Code\helpers\ClassHelper;
use PhpLab\Core\Legacy\Yii\Helpers\Inflector;
use PhpLab\Dev\Generator\Domain\Enums\TypeEnum;
use PhpLab\Dev\Generator\Domain\Helpers\LocationHelper;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\InterfaceGenerator;
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
            $fileGenerator->setUse('PhpLab\Core\Domain\Interfaces\Repository\CrudRepositoryInterface');
            $interfaceGenerator->setImplementedInterfaces(['CrudRepositoryInterface']);
        }
        $fileGenerator->setNamespace($this->domainNamespace . '\\' . $this->interfaceDir());
        $fileGenerator->setClass($interfaceGenerator);
        ClassHelper::generateFile($fileGenerator->getNamespace() . '\\' . $this->getInterfaceName(), $fileGenerator->generate());


        /*$className = $this->getClassName();
        $interfaceEntity = new InterfaceEntity;
        $interfaceEntity->name = $this->getInterfaceFullName($className);
        if($this->buildDto->isCrudRepository) {
            $uses[] = new ClassUseEntity(['name' => 'PhpLab\Core\Domain\Interfaces\Repository\CrudRepositoryInterface']);
            $interfaceEntity->extends = 'CrudRepositoryInterface';
        }
        ClassHelper::generate($interfaceEntity, $uses);
        return $interfaceEntity;*/
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
        //$fullClassName = $this->getFullClassName();
        $fileGenerator = new FileGenerator;
        $classGenerator = new ClassGenerator;
        $fileGenerator->setNamespace($this->domainNamespace . '\\' . $this->classDir() . '\\' . $driverDirName);

        $parentClass = $this->parentClass($driver);
        if($parentClass) {
            $fileGenerator->setUse($parentClass);
            $classGenerator->setExtendedClass(basename($parentClass));
        } else {
            $fileGenerator->setUse(ForgeEntityTrait::class);
            $classGenerator->addUse('ForgeEntityTrait');
        }

        $classGenerator->setName($className);
        if ($this->isMakeInterface()) {
            $classGenerator->setImplementedInterfaces([$this->getInterfaceName()]);
            $fileGenerator->setUse($this->getInterfaceFullName());
        }

        $entityFullClassName = $this->domainNamespace . LocationHelper::fullClassName($this->name, TypeEnum::ENTITY);
        //$fileGenerator->setUse($entityFullClassName);
        $entityClassName = basename($entityFullClassName);
        $classGenerator->addProperties([
            [Inflector::variablize('tableName'), "{$this->buildDto->domainName}_{$this->buildDto->name}", PropertyGenerator::FLAG_PROTECTED],
            [Inflector::variablize('entityClass'), $entityFullClassName, PropertyGenerator::FLAG_PROTECTED],
        ]);


        $fileGenerator->setClass($classGenerator);
        ClassHelper::generateFile($fileGenerator->getNamespace() . '\\' . $className, $fileGenerator->generate());


        /*$className = $this->getClassName();
        $uses = [];
        $classEntity = new ClassEntity;
        $classEntity->name = $this->domainNamespace . '\\' . $this->classDir() . '\\' . $repoClassName;
        if($this->isMakeInterface()) {
            $useEntity = new ClassUseEntity;
            $useEntity->name = $this->getInterfaceFullName();
            $uses[] = $useEntity;
            $classEntity->implements = $this->getInterfaceName();
        }
$uses[] = new ClassUseEntity(['name' => $entityFullClassName]);

        $parentClass = $this->parentClass($driver);
        $uses[] = new ClassUseEntity(['name' => $parentClass]);
        $classEntity->extends = basename($parentClass);

        $classEntity->code = "
    protected \$tableName = '{$this->buildDto->domainName}_{$this->buildDto->name}';
    protected \$entityClass = {$entityClassName}::class;
";

        ClassHelper::generate($classEntity, $uses);
        return $classEntity;*/
    }

    private function parentClass($driver)
    {
        $className = '';
        if ('eloquent' == $driver) {
            if ($this->buildDto->isCrudRepository) {
                $className = 'PhpLab\Eloquent\Db\Base\BaseEloquentCrudRepository';
            } else {
                $className = 'PhpLab\Eloquent\Db\Base\BaseEloquentRepository';
            }
        } else {
            //$className = 'PhpLab\Core\Domain\Base\BaseRepository';
        }
        return $className;
    }

}
