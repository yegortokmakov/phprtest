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
class ProviderAnnotation extends Annotation implements IAnnotationParser
{
    protected $providerMethod = null;

    public static function parseAnnotation($value)
    {
        $parsed = explode(' ', trim($value));

        if (count($parsed) > 1) {
            throw new AnnotationException('Invalid @provider annotation: incorrect method name "' . $value . '"');
        }

        return ['providerMethod' => $parsed[0]];
    }

    public function getProvider()
    {
        return $this->providerMethod;
    }
}
