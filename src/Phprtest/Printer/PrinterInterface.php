<?php

namespace Phprtest\Printer;

interface PrinterInterface
{
    public function render(array $output, array $status);
}