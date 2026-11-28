<?php
declare(strict_types=1);

abstract class Student
{
    public function __construct(
        protected string $id,
        protected string $name,
        protected float $baseFee
    ) {}

    public function getId(): string { return $this->id; }
    public function getName(): string { return $this->name; }

    abstract public function calculateFee(): float;

    public function summary(): string
    {
        return $this->id . ' - ' . $this->name;
    }
}

class RegularStudent extends Student
{
    public function calculateFee(): float { return $this->baseFee; }
}

class ScholarshipStudent extends Student
{
    public function __construct(
        string $id, string $name, float $baseFee,
        private float $discountPercent
    ) {
        if ($discountPercent < 0 || $discountPercent > 100) {
            throw new InvalidArgumentException('Invalid discount');
        }
        parent::__construct($id, $name, $baseFee);
    }

    public function calculateFee(): float
    {
        return $this->baseFee * (1 - $this->discountPercent / 100);
    }

    public function summary(): string
    {
        return parent::summary() . ' [Scholarship]';
    }
}

class InternationalStudent extends Student
{
    public function __construct(
        string $id, string $name, float $baseFee,
        private float $internationalFee
    ) {
        parent::__construct($id, $name, $baseFee);
    }

    public function calculateFee(): float
    {
        return $this->baseFee + $this->internationalFee;
    }

    public function summary(): string
    {
        return parent::summary() . ' [International]';
    }
}

class StudentRepository
{
    /** @var Student[] */
    private array $items = [];

    public function save(Student $student): void
    {
        $this->items[$student->getId()] = $student;
    }

    public function all(): array
    {
        return array_values($this->items);
    }
}
