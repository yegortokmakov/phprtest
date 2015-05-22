<?php

return array(
  '#namespace' => 'Phpperftest\\Annotations',
  '#uses' => array (
  'Annotation' => 'mindplay\\annotations\\Annotation',
  'IAnnotationParser' => 'mindplay\\annotations\\IAnnotationParser',
),
  'Phpperftest\\Annotations\\AssertAnnotation' => array(
    array('#name' => 'usage', '#type' => 'mindplay\\annotations\\UsageAnnotation', 'method'=>true, 'class'=>true, 'inherited'=>true)
  ),
);

