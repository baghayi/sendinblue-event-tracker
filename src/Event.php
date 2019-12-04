<?php

declare(strict_types=1);

namespace Baghayi\Sendinblue;

class Event
{
    public $name;
    public $properties = [];

    public function __construct(string $name, array $properties = [])
    {
        $this->name = $name;
        $this->properties = $properties;
    }
}
