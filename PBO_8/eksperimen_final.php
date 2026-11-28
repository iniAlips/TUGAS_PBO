<?php
class BaseReport
{
    final public function getId(): string { return 'R-01'; }
}

class CustomReport extends BaseReport
{
    public function getId(): string { return 'R-99'; }
}
