<?php

return array(
  '#namespace' => 'testname',
  '#uses' => array (
  'TestSuite' => 'Phpperftest\\TestSuite',
),
  'testname\\DummyTest::testSimple' => array(
    array('#name' => 'assert', '#type' => 'Phpperftest\\Annotations\\AssertAnnotation', '0' => 'memoryUsage', '1' => 1, '2' => 3),
    array('#name' => 'assert', '#type' => 'Phpperftest\\Annotations\\AssertAnnotation', '0' => 'timeUsage', '1' => 1),
    array('#name' => 'assert', '#type' => 'Phpperftest\\Annotations\\AssertAnnotation', '0' => 'memoryPeakUsage', '1' => 10, '2' => 9999999)
  ),
  'testname\\DummyTest::testSimple2' => array(
    array('#name' => 'assert', '#type' => 'Phpperftest\\Annotations\\AssertAnnotation', '0' => 'memoryUsage', '1' => 1, '2' => 3),
    array('#name' => 'assert', '#type' => 'Phpperftest\\Annotations\\AssertAnnotation', '0' => 'timeUsage', '1' => 1),
    array('#name' => 'assert', '#type' => 'Phpperftest\\Annotations\\AssertAnnotation', '0' => 'memoryPeakUsage', '1' => 10)
  ),
);

