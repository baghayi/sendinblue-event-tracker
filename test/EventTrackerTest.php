<?php

declare(strict_types=1);

namespace Test;

use Baghayi\Sendinblue\EventTracker;
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
}
