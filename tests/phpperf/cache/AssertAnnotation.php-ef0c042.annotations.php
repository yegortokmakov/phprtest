<?php

return array(
  '#namespace' => 'Phpperftest\\Annotations',
  '#uses' => array (
  'Annotation' => 'mindplay\\annotations\\Annotation',
  'AnnotationException' => 'mindplay\\annotations\\AnnotationException',
  'IAnnotationParser' => 'mindplay\\annotations\\IAnnotationParser',
),
  'Phpperftest\\Annotations\\AssertAnnotation' => array(
    array('#name' => 'usage', '#type' => 'mindplay\\annotations\\UsageAnnotation', 'multiple'=>true, 'method'=>true, 'class'=>true)
  ),
);

