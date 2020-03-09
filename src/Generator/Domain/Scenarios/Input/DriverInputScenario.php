<?php

namespace PhpLab\Dev\Generator\Domain\Scenarios\Input;

use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class DriverInputScenario extends BaseInputScenario
{

    protected function paramName()
    {
        return 'driver';
    }

    protected function question(): Question
    {
        $drivers = [
            'eloquent',
            'file',
        ];
        //$drivers['c'] = 'custom';
        $question = new ChoiceQuestion(
            'Please select repository drivers',
            $drivers
        );
        $question->setMultiselect(true);
        return $question;
    }

}
