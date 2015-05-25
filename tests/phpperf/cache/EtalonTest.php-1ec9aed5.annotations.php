<?php

return array(
  '#namespace' => 'Phpperftest',
  '#uses' => array (
  'TestSuite' => 'Phpperftest\\TestSuite',
),
  'Phpperftest\\EtalonTest::testTime' => array(
    array('#name' => 'assert', '#type' => 'Phpperftest\\Annotations\\AssertAnnotation', '0' => 'timeUsage', '1' => 1)
  ),
);

