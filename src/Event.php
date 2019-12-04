<?php

declare(strict_types=1);

namespace Baghayi\Sendinblue;

class Event
{
    private $name;
    private $properties = [];
    private $eventdata = [];

    public function __construct(string $name, array $properties = [], array $eventdata = [])
    {
        $this->name = $name;
        $this->properties = $properties;
        $this->eventdata = $eventdata;
    }

    public function toArray(): array
    {
        return ['event' => $this->name] + $this->getEventData() + $this->getProperties();
    }

    private function getEventData(): array
    {
        if (empty($this->eventdata))
            return [];

        return [
            'eventdata' => [
                'data' => $this->eventdata,
            ]
        ];
    }

    private function getProperties(): array
    {
        if (empty($this->properties))
            return [];

        return [
            'properties' => $this->properties,
        ];
    }
}
