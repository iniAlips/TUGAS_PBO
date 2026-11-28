<?php
declare(strict_types=1);

class StudentPresenterV2
{
    public function __call(string $name, array $args): string
    {
        if ($name !== 'showData') {
            throw new BadMethodCallException('Unknown method: ' . $name);
        }
        if (count($args) !== 3) {
            throw new InvalidArgumentException('Expected 3 strings');
        }
        foreach ($args as $arg) {
            if (!is_string($arg)) {
                throw new InvalidArgumentException('Expected 3 strings');
            }
        }
        [$id, $nama, $jurusan] = $args;
        return "ID: $id, Name: $nama, Major: $jurusan";
    }
}

$view = new StudentPresenterV2();
echo $view->showData('2026001', 'Ahmad', 'Informatika') . PHP_EOL;

foreach ([['Ahmad'], ['2026001', 'Ahmad'], [1, 2, 3]] as $args) {
    try {
        echo $view->showData(...$args) . PHP_EOL;
    } catch (InvalidArgumentException $e) {
        echo get_class($e) . ': ' . $e->getMessage() . PHP_EOL;
    }
}
