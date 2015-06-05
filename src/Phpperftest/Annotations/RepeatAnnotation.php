<?php

namespace Phpperftest\Annotations;

use mindplay\annotations\Annotation;
use mindplay\annotations\AnnotationException;
use mindplay\annotations\IAnnotationParser;

/**
 * Defines a method-parameter's type
 *
 * @usage('multiple'=>false, 'method'=>true, 'class'=>true)
 */
class RepeatAnnotation extends Annotation implements IAnnotationParser
{
    protected $repeatTimes = null;

    public static function parseAnnotation($value)
    {
        $parsed = explode(' ', trim($value));

        $repeatTimes = intval($parsed[0]);

        if (count($parsed) != 1 || $repeatTimes < 1) {
            throw new AnnotationException('Invalid @repeat annotation: incorrect repeat value "' . $value . '"');
        }

        return ['repeatTimes' => $repeatTimes];
    }

    public function getRepeatTimes()
    {
        return $this->repeatTimes;
    }
}
