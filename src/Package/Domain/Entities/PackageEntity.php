<?php

namespace PhpLab\Dev\Package\Domain\Entities;

use PhpLab\Core\Domain\Interfaces\Entity\EntityIdInterface;

class PackageEntity implements EntityIdInterface
{

    private $id;
    private $name;
    private $group;
    private $directory;

    public function getId()
    {
        return $this->group->name . '/' . $this->name;
    }

    public function setId($id)
    {
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
