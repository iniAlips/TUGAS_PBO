<?php
declare(strict_types=1);
require_once 'Calculator.php';

$calc = new Calculator();
echo $calc->add(10, 20) . PHP_EOL;        // 30
echo $calc->add(10, 20, 30) . PHP_EOL;    // 60
