<?php

declare(strict_types=1);

namespace Test;

use Baghayi\Sendinblue\Event;
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
        $service = new EventTracker($client, 'api-key');
        $service->track(new Event('my_event'), new Email('sb@domain.com'));

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
        $service = new EventTracker($client, 'api-key');
        $service->track(new Event('my_event'), new Email('sb@domain.com'));
        $this->assertSame('POST', $container[0]['request']->getMethod());
        $this->assertSame('https://in-automate.sendinblue.com/api/v2/trackEvent', (string) $container[0]['request']->getUri());
    }

    /**
    * @test
    */
    public function need_to_authorize_for_our_request_to_get_processed()
    {
        $container = [];
        $client = $this->getGuzzleClient($container);
        $service = new EventTracker($client, 'api-key-token');
        $service->track(new Event('my_event'), new Email('sb@domain.com'));
        $this->assertArrayHasKey('ma-key', $container[0]['request']->getHeaders());
        $this->assertContains('api-key-token', $container[0]['request']->getHeaders()['ma-key']);
    }

    /**
    * @test
    */
    public function can_send_properties_along_the_request()
    {
        $container = [];
        $client = $this->getGuzzleClient($container);
        $service = new EventTracker($client, 'api-key-token');
        $properties = [
            'test' => 12345
        ];
        $service->track(new Event('my_event', $properties), new Email('sb@domain.com'));
        $this->assertArrayHasKey('properties', $this->getRequestData($container));
        $this->assertArrayHasKey('test', $this->getRequestData($container)['properties']);
        $this->assertSame(12345, $this->getRequestData($container)['properties']['test']);
    }

    /**
    * @test
    */
    public function sends_properties_whenever_there_is_data()
    {
        $container = [];
        $client = $this->getGuzzleClient($container);
        $service = new EventTracker($client, 'api-key-token');
        $service->track(new Event('my_event'), new Email('sb@domain.com'));
        $this->assertArrayNotHasKey('properties', $this->getRequestData($container));
    }

    /**
    * @test
    */
    public function can_pass_eventdata()
    {
        $container = [];
        $client = $this->getGuzzleClient($container);
        $service = new EventTracker($client, 'api-key-token');
        $eventdata = [
            'mylist' => [1, 2, 3]
        ];
        $service->track(new Event('my_event', [], $eventdata), new Email('sb@domain.com'));
        $this->assertArrayHasKey('eventdata', $this->getRequestData($container));
        $this->assertArrayHasKey('data', $this->getRequestData($container)['eventdata']);
        $this->assertArrayHasKey('mylist', $this->getRequestData($container)['eventdata']['data']);
        $this->assertSame([1, 2, 3], $this->getRequestData($container)['eventdata']['data']['mylist']);
    }

    /**
    * @test
    */
    public function only_send_eventdata_when_there_is_data()
    {
        $container = [];
        $client = $this->getGuzzleClient($container);
        $service = new EventTracker($client, 'api-key-token');
        $service->track(new Event('my_event'), new Email('sb@domain.com'));
        $this->assertArrayNotHasKey('eventdata', $this->getRequestData($container));
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
