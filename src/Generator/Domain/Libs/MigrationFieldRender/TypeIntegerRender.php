<?php

namespace PhpLab\Dev\Generator\Domain\Libs\MigrationFieldRender;

use PhpLab\Dev\Generator\Domain\Helpers\FieldRenderHelper;

class TypeIntegerRender extends BaseRender
{

    public function isMatch(): bool
    {
        return FieldRenderHelper::isMatchSuffix($this->attributeName, '_id');
    }

    public function run(): string
    {
        return $this->renderCode('integer', $this->attributeName);
    }

}
