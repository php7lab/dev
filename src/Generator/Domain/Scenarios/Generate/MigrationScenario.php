<?php

namespace PhpLab\Dev\Generator\Domain\Scenarios\Generate;

use PhpLab\Core\Legacy\Yii\Helpers\FileHelper;
use PhpLab\Dev\Generator\Domain\Helpers\TemplateCodeHelper;
use PhpLab\Dev\Package\Domain\Helpers\PackageHelper;
use Zend\Code\Generator\FileGenerator;

class MigrationScenario extends BaseScenario
{

    public function typeName()
    {
        return 'Migration';
    }

    public function classDir()
    {
        return 'Migrations';
    }

    protected function getClassName(): string
    {
        $timeStr = date('Y_m_d_His');
        $className = "m_{$timeStr}_create_{$this->name}_table";
        return $className;
    }

    protected function createClass()
    {

        $fileGenerator = new FileGenerator;

        $fileGenerator->setNamespace('Migrations');
        $fileGenerator->setUse('Illuminate\Database\Schema\Blueprint');
        $fileGenerator->setUse('PhpLab\Eloquent\Migration\Base\BaseCreateTableMigration');

        $tableName = $this->buildDto->domainName . '_' . $this->buildDto->name;
        $code = TemplateCodeHelper::generateMigrationClassCode($this->getClassName(), $this->buildDto->attributes, $tableName);

        $fileGenerator->setBody($code);
        $fileName = $this->getFileName();
        FileHelper::save($fileName, $fileGenerator->generate());
    }

    private function getFileName()
    {
        $className = $this->getClassName();
        $dir = PackageHelper::pathByNamespace($this->buildDto->domainNamespace . '/' . $this->classDir());
        $fileName = $dir . '/' . $className . '.php';
        return $fileName;
    }

}
