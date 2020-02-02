<?php

namespace PhpLab\Dev\Generator\Domain\Scenarios\Generate;

use PhpLab\Core\Legacy\Code\entities\ClassEntity;
use PhpLab\Core\Legacy\Code\entities\ClassUseEntity;
use PhpLab\Core\Legacy\Code\entities\ClassVariableEntity;
use PhpLab\Core\Legacy\Code\entities\InterfaceEntity;
use PhpLab\Core\Legacy\Code\enums\AccessEnum;
use PhpLab\Core\Legacy\Code\helpers\ClassHelper;
use PhpLab\Core\Legacy\Yii\Helpers\FileHelper;
use PhpLab\Dev\Generator\Domain\Helpers\TemplateCodeHelper;
use PhpLab\Dev\Package\Domain\Helpers\PackageHelper;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;

class ApiScenario extends BaseScenario
{

    public function typeName()
    {
        return 'Controller';
    }

    public function classDir()
    {
        return 'Controllers';
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

        if ($this->buildDto->isCrudController) {
            $fileGenerator->setUse('PhpLab\Rest\Base\BaseCrudApiController');
            $classGenerator->setExtendedClass('BaseCrudApiController');
        } else {
            $fileGenerator->setUse('Symfony\Bundle\FrameworkBundle\Controller\AbstractController');
            $classGenerator->setExtendedClass('AbstractController');
        }
        $classGenerator->addProperties([
            ['service', null, PropertyGenerator::FLAG_PRIVATE]
        ]);

        $fileGenerator->setUse('Symfony\Bundle\FrameworkBundle\Controller\AbstractController');
        $fileGenerator->setUse('PhpLab\Web\Traits\AccessTrait');

        $classGenerator->setExtendedClass('AbstractController');
        $classGenerator->addTrait('AccessTrait');


        $parameterGenerator = new ParameterGenerator;
        $parameterGenerator->setName('service');
        $parameterGenerator->setType('ExampleService');

        $methodGenerator = new MethodGenerator;
        $methodGenerator->setName('__construct');
        $methodGenerator->setParameter($parameterGenerator);
        $methodGenerator->setBody('$this->service = $service;');
        $classGenerator->addMethods([$methodGenerator]);

        $fileGenerator->setNamespace($this->buildDto->moduleNamespace . '\\' . $this->classDir());
        $fileGenerator->setClass($classGenerator);

        ClassHelper::generateFile($fileGenerator->getNamespace() . '\\' . $className, $fileGenerator->generate());

        $this->generateRouteConfig($fileGenerator->getNamespace() . '\\' . $className);
    }

    private function generateRouteConfig($classFullName)
    {
        $path = PackageHelper::pathByNamespace($this->buildDto->moduleNamespace);
        $routesConfigFile = $path . '/config/routes.yaml';
        FileHelper::save($routesConfigFile, TemplateCodeHelper::generateCrudApiRoutesConfig($this->buildDto, $classFullName));
    }
}
