<?php

namespace PhpLab\Dev\Generator\Domain\Libs\MigrationFieldRender;

use PhpLab\Dev\Generator\Domain\Helpers\FieldRenderHelper;

class TypeTimeRender extends BaseRender
{

    public function isMatch(): bool
    {
        return FieldRenderHelper::isMatchSuffix($this->attributeName, '_at');
    }

    public function run(): string
    {
        return $this->renderCode('dateTime', $this->attributeName);
    }

}
