<?php

declare(strict_types=1);

namespace Test;

use Baghayi\Sendinblue\EventTracker;
use Baghayi\Value\Email;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class EventTrackerTest extends TestCase
{
    /**
    * @test
    */
    public function events_tracker_service()
    {
        $this->assertTrue(class_exists(EventTracker::class), 'service does not exists');
    }

    /**
    * @test
    */
    public function tracks_an_event_occured_by_a_contact()
    {
        $container = [];
        $client = $this->getGuzzleClient($container);
        $service = new EventTracker($client);
        $service->track('my_event', new Email('sb@domain.com'));

        $data = $this->getRequestData($container);
        $this->assertSame('sb@domain.com', $data['email']);
        $this->assertSame('my_event', $data['event']);
    }

    /**
    * @test
    */
    public function posts_data_to_appropriate_api()
    {
        $container = [];
        $client = $this->getGuzzleClient($container);
        $service = new EventTracker($client);
        $service->track('my_event', new Email('sb@domain.com'));
        $this->assertSame('POST', $container[0]['request']->getMethod());
        $this->assertSame('https://in-automate.sendinblue.com/api/v2/trackEvent', (string) $container[0]['request']->getUri());
    }

    private function getGuzzleClient(array &$container): Client
    {
        $history = Middleware::history($container);
        $mockHandler = new MockHandler([ new Response(200) ]);
        $stack = HandlerStack::create($mockHandler);
        $stack->push($history);
        $client = new Client(['handler' => $stack]);
        return $client;
    }

    private function getRequestData(array $container)
    {
        $body = json_decode((string) $container[0]['request']->getBody(), true);
        return $body;
    }
}
