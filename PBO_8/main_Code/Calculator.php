<?php
declare(strict_types=1);

class Calculator
{
    public function add(int ...$numbers): int
    {
        return array_sum($numbers);
    }
}
