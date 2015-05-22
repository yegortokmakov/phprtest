<?php

return array(
  '#namespace' => '',
  '#uses' => array (
  'TestSuite' => 'Phpperftest\\TestSuite',
),
  'DummyTest::testSimple' => array(
    array('#name' => 'memoryUsage', '#type' => 'Phpperftest\\Annotations\\MemoryUsageAnnotation', '0' => '2', '1' => '3'),
    array('#name' => 'timeUsage', '#type' => 'Phpperftest\\Annotations\\TimeUsageAnnotation', '0' => '1')
  ),
);

