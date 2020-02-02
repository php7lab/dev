<?php

namespace PhpLab\Dev\Package\Domain\Entities;

class PackageEntity
{

    private $id;
    private $name;
    private $group;
    private $directory;

    public function getId()
    {
        return $this->group->name . '/' . $this->name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function setGroup(GroupEntity $group): void
    {
        $this->group = $group;
    }

    public function getDirectory()
    {
        $vendorDir = realpath(__DIR__ . '/../../../../../..');
        return $vendorDir . DIRECTORY_SEPARATOR . $this->getId();
    }

}
