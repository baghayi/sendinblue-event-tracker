<?php

declare(strict_types=1);

namespace Baghayi\Sendinblue;

class Event
{
    public $name;
    public $properties = [];
    public $eventdata = [];

    public function __construct(string $name, array $properties = [], array $eventdata = [])
    {
        $this->name = $name;
        $this->properties = $properties;
        $this->eventdata = $eventdata;
    }
}
