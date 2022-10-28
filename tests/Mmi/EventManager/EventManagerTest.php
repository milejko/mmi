<?php

namespace Tests\Mmi\EventManager;

use _PHPStan_76800bfb5\Nette\Neon\Exception;
use Mmi\App\App;
use Mmi\App\AppMvcEvents;
use Mmi\App\TestApp;
use Mmi\EventManager\EventManager;
use Mmi\EventManager\ResponseCollection;
use Mmi\Mvc\View;

class EventManagerTest extends \PHPUnit\Framework\TestCase
{
    private function getEventManagerInstance() : EventManager
    {
        /** @var App $app */
        $app = TestApp::$di->get(App::class);
        /** @var EventManager $eventManager */
        $eventManager = $app::$di->get(EventManager::class);

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
