<?php

namespace Tests\Mmi\EventManager;

use Mmi\App\App;
use Mmi\App\AppMvcEvents;
use Mmi\App\AppTesting;
use Mmi\EventManager\EventManagerInterface;
use Mmi\EventManager\ResponseCollection;

class EventManagerTest extends \PHPUnit\Framework\TestCase
{
    private function getEventManagerInstance(): EventManagerInterface
    {
        /** @var App $app */
        $app = AppTesting::$di->get(App::class);
        /** @var EventManagerInterface $eventManager */
        $eventManager = $app::$di->get(EventManagerInterface::class);

        return $eventManager;
    }

    public function testEvent()
    {
        $eventManager = $this->getEventManagerInstance();

        $eventManager->attach(AppMvcEvents::EVENT_FINISH, function ($request) {
            return 'test-mvc-events';
        }, 1);

        $result = $eventManager->trigger(AppMvcEvents::EVENT_FINISH, null, []);

        $this->assertInstanceOf(ResponseCollection::class, $result);
        $this->assertCount(1, $eventManager->getListeners(AppMvcEvents::EVENT_FINISH));
        $this->assertEquals('test-mvc-events', $result[0]);
    }

    public function testAddAndRemoveEvent()
    {
        $eventManager = $this->getEventManagerInstance();

        $eventManager->attach(AppMvcEvents::EVENT_FINISH, function ($request) {
            return 'test-mvc-events';
        }, 1);

        $eventManager->detach(null, AppMvcEvents::EVENT_FINISH);

        $result = $eventManager->trigger(AppMvcEvents::EVENT_FINISH, null, []);
        $this->assertCount(0, (array)$eventManager->getListeners(AppMvcEvents::EVENT_FINISH));
    }
}
