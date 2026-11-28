<?php
declare(strict_types=1);

class StudentPresenter
{
    public function __call(string $name, array $args): string
    {
        if ($name !== 'showData') {
            throw new BadMethodCallException('Unknown method: ' . $name);
        }
        if (count($args) === 1 && is_string($args[0])) {
            return 'Name: ' . $args[0];
        }
        if (count($args) === 2 && is_string($args[0])
            && is_string($args[1])) {
            return 'ID: ' . $args[0] . ', Name: ' . $args[1];
        }
        throw new InvalidArgumentException('Expected 1 or 2 strings');
    }
}
