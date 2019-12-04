<?php

declare(strict_types=1);

namespace Baghayi\Sendinblue;

use Baghayi\Value\Email;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class EventTracker
{
    const EVENT_TRACKER_URI = 'http://xyz.com/xyz';
    private $http;

    public function __construct(Client $http)
    {
        $this->http = $http;
    }

    public function track(string $event, Email $contact)
    {
        $request = new Request('GET', self::EVENT_TRACKER_URI, [], json_encode([
            'email' => (string) $contact,
            'event' => $event,
        ]));
        $this->http->send($request);
    }
}
