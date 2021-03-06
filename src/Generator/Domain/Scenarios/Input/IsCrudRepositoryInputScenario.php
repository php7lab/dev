<?php

namespace PhpLab\Dev\Generator\Domain\Scenarios\Input;

use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class IsCrudRepositoryInputScenario extends BaseInputScenario
{

    protected function paramName()
    {
        return 'isCrudRepository';
    }

    protected function question(): Question
    {
        $question = new ConfirmationQuestion(
            'Is CRUD repository? (y|n): ',
            false
        );
        return $question;
    }

}
