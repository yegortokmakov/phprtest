<?php

namespace Phpperftest\Annotations;

use mindplay\annotations\Annotation;
use mindplay\annotations\AnnotationException;
use mindplay\annotations\IAnnotationParser;

/**
 * Defines a method-parameter's type
 *
 * @usage('multiple'=>true, 'method'=>true, 'class'=>true)
 */
class AssertAnnotation extends Annotation implements IAnnotationParser
{
    public $metric = null;

    public $softLimit = null;

    public $hardLimit = null;

    protected static $metrics = array('memoryUsage', 'timeUsage');

    /**
     * Initialize the annotation.
     */
    public function initAnnotation(array $properties)
    {
        if (count($properties) == 2) {
            $this->metric = $properties[0];
            $this->hardLimit = $properties[1];
        } elseif (count($properties) == 3) {
            $this->metric = $properties[0];
            $this->softLimit = $properties[1];
            $this->hardLimit = $properties[2];
        }

        $this->map($properties, array('metric', 'softLimit', 'hardLimit'));

        parent::initAnnotation($properties);
    }

    public static function parseAnnotation($value)
    {
        $parsed = explode(' ', trim($value), 3);

        if (count($parsed) < 2) {
            throw new AnnotationException('Invalid @assert annotation: >2 properties required');
        }

        $metric = array_shift($parsed);

        if (!in_array($metric, self::$metrics)) {
            throw new AnnotationException('Invalid @assert annotation: unknown metric name' . $metric);
        }

        // https://bugs.php.net/bug.php?id=55416
        $parsed = @array_map(function ($limit) {
            $limit = floatval($limit);
            if ($limit == 0) {
                throw new AnnotationException('Invalid @assert annotation: limit must be >0');
            }
            return $limit;
        }, $parsed);

        array_unshift($parsed, $metric);

        return $parsed;
    }
}
