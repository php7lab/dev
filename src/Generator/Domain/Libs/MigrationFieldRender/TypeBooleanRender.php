<?php

namespace PhpLab\Dev\Generator\Domain\Libs\MigrationFieldRender;

use PhpLab\Dev\Generator\Domain\Helpers\FieldRenderHelper;

class TypeBooleanRender extends BaseRender
{

    public function isMatch(): bool
    {
        return FieldRenderHelper::isMatchPrefix($this->attributeName, 'is_');
    }

    public function run(): string
    {
        return $this->renderCode('boolean', $this->attributeName);
    }

}
