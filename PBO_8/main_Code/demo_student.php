<?php
declare(strict_types=1);
require_once 'Student.php';

$repo = new StudentRepository();
$repo->save(new RegularStudent('M01', 'Alya', 5000000));
$repo->save(new ScholarshipStudent('M02', 'Bima', 5000000, 40));
$repo->save(new InternationalStudent('M03', 'Chen', 5000000, 3000000));

foreach ($repo->all() as $student) {
    printf('%s | Rp%.0f%s', $student->summary(),
        $student->calculateFee(), PHP_EOL);
}
