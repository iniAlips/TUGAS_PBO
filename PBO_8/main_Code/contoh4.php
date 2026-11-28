<?php
declare(strict_types=1);

class Student
{
    public function getStatus(): string
    {
        return 'Active Student';
    }
}

class ScholarshipStudent extends Student
{
    public function getStatus(): string
    {
        return parent::getStatus() . ' - Scholarship Recipient';
    }
}

$students = [new Student(), new ScholarshipStudent()];
foreach ($students as $student) {
    echo $student->getStatus() . PHP_EOL;
}
