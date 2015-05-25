<?php

namespace Phpperftest\Printer;

interface PrinterInterface
{
    public function render($results, $status);
}