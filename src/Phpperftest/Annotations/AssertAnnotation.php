<?php

namespace Phpperftest\Annotations;

use mindplay\annotations\Annotation;
use mindplay\annotations\IAnnotationParser;

/**
 * Defines a method-parameter's type
 *
 * @usage('method'=>true, 'class'=>true, 'inherited'=>true)
 */
class AssertAnnotation extends Annotation implements IAnnotationParser
{
    public $softLimit = null;

    public $hardLimit = null;

    /**
     * Initialize the annotation.
     */
    public function initAnnotation(array $properties)
    {
        //@todo: wtf is multiple?
        $this->map($properties, array('softLimit', 'hardLimit'));

        parent::initAnnotation($properties);
    }

    public static function parseAnnotation($value)
    {
        //@todo: validation
        //@todo: integer
        return explode(' ', trim($value), 3);
    }
}
