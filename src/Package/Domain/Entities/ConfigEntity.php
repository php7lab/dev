<?php

namespace PhpLab\Dev\Package\Domain\Entities;

use PhpLab\Core\Domain\Interfaces\Entity\EntityIdInterface;
use PhpLab\Core\Legacy\Yii\Helpers\ArrayHelper;
use Symfony\Component\Validator\Constraints as Assert;
use PhpLab\Core\Domain\Interfaces\Entity\ValidateEntityInterface;

class ConfigEntity implements ValidateEntityInterface, EntityIdInterface
{

    private $id = null;

    private $config = null;

    /** @var PackageEntity */
    private $package = null;

    private $name = null;

    private $type = null;

    private $authors = null;

    private $license = null;

    private $minimumStability = null;

    private $require = null;

    private $requireDev = null;

    private $autoload = null;

    private $autoloadDev = null;

    public function validationRules()
    {
        return [
            'name' => [
                new Assert\NotBlank,
            ],
            'type' => [
                new Assert\NotBlank,
            ],
            'authors' => [
                new Assert\NotBlank,
            ],
            'license' => [
                new Assert\NotBlank,
            ],
            'minimumStability' => [
                new Assert\NotBlank,
            ],
            'require' => [
                new Assert\NotBlank,
            ],
            'requireDev' => [
                new Assert\NotBlank,
            ],
            'autoload' => [
                new Assert\NotBlank,
            ],
            'autoloadDev' => [
                new Assert\NotBlank,
            ],
        ];
    }

    public function getId()
    {
        return $this->package->getId();
    }

    public function setId($id)
    {
        //$this->id = $id;
    }

    public function getPackage(): PackageEntity
    {
        return $this->package;
    }

    public function setPackage(PackageEntity $package)
    {
        $this->package = $package;
    }

    public function getConfigItem($name, $default = null)
    {
        return ArrayHelper::getValue($this->config, $name, $default);
    }

    public function setConfigItem(string $name, $value)
    {
        ArrayHelper::setValue($this->config, $name, $value);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function setName($value) : void
    {
        $this->setConfigItem('name', $value);
    }

    public function getName()
    {
        return $this->getConfigItem('name');
    }

    public function setType($value) : void
    {
        $this->setConfigItem('type', $value);
    }

    public function getType()
    {
        return $this->getConfigItem('type');
    }

    public function setAuthors($value) : void
    {
        $this->setConfigItem('authors', $value);
    }

    public function getAuthors()
    {
        return $this->getConfigItem('authors', []);
    }

    public function setLicense($value) : void
    {
        $this->setConfigItem('license', $value);
    }

    public function getLicense()
    {
        return $this->getConfigItem('license');
    }

    public function setMinimumStability($value) : void
    {
        $this->setConfigItem('minimum-stability', $value);
    }

    public function getMinimumStability()
    {
        return $this->getConfigItem('minimum-stability');
    }

    public function setRequire($value) : void
    {
        $this->setConfigItem('require', $value);
    }

    public function getAllRequire()
    {
        return array_merge($this->getConfigItem('require', []), $this->getConfigItem('require-dev', []));
    }

    public function getRequire()
    {
        return $this->getConfigItem('require', []);
    }

    public function setRequireDev($value) : void
    {
        $this->setConfigItem('require-dev', $value);
    }

    public function getRequireDev()
    {
        return $this->getConfigItem('require-dev', []);
    }

    public function setAutoload($value) : void
    {
        $this->setConfigItem('autoload', $value);
    }

    public function getAllAutoload()
    {
        return array_merge($this->getConfigItem('autoload', []), $this->getConfigItem('autoload-dev', []));
    }

    public function getAllAutoloadPsr4()
    {
        $psr4autoloads = $this->getConfigItem('autoload.psr-4', []);
        $psr4devAutoloads = $this->getConfigItem('autoload-dev.psr-4', []);
        return array_merge($psr4autoloads, $psr4devAutoloads);
    }

    public function getAutoloadItem($name)
    {
        return $this->getConfigItem(['autoload', $name], []);
    }

    public function getAutoload()
    {
        return $this->getConfigItem('autoload', []);
    }

    public function setAutoloadDev($value) : void
    {
        $this->setConfigItem('autoload-dev', $value);
    }

    public function getAutoloadDevItem($name)
    {
        return $this->getConfigItem(['autoload-dev', $name], []);
    }

    public function getAutoloadDev()
    {
        return $this->getConfigItem('autoload-dev', []);
    }

}

