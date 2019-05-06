<?php

namespace Mmi\EventListener;

use Mmi\Session\Session;
use Mmi\Session\SessionConfig;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class KernelEventSubscriber
 * @package Mmi\EventListener
 */
class KernelEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Session
     */
    private $session;
    
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => [
                ['warmUpSession']
            ]
        ];
    }
    
    /**
     * @param GetResponseEvent $event
     */
    public function warmUpSession(GetResponseEvent $event)
    {
        $this->session->start(new SessionConfig());
    }
}
