<?php

declare(strict_types=1);

namespace Baghayi\Sendinblue;

use Baghayi\Value\Email;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class EventTracker
{
    const EVENT_TRACKER_URI = 'https://in-automate.sendinblue.com/api/v2/trackEvent';
    private $http;
    private $apiKey;

    public function __construct(Client $http, string $apiKey)
    {
        $this->http = $http;
        $this->apiKey = $apiKey;
    }

    public function track(Event $event, Email $contact)
    {
        $request = new Request('POST', self::EVENT_TRACKER_URI, $this->getHeaders(), $this->getData($contact, $event));
        $this->http->send($request);
    }

    private function getData(Email $contact, Event $event): string
    {
        return json_encode([
            'email' => (string) $contact,
            'event' => $event->name,
        ] + $this->getProperties($event) + $this->getEventData($event));
    }

    private function getHeaders(): array
    {
        return ['ma-key' => $this->apiKey];
    }

    private function getEventData(Event $event): array
    {
        if (empty($event->eventdata))
            return [];

        return [
            'eventdata' => [
                'data' => $event->eventdata,
            ]
        ];
    }

    private function getProperties(Event $event): array
    {
        return [
            'properties' => $event->properties,
        ];
    }
}
