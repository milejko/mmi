<?php

namespace Mmi\EventListener;

use Mmi\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class KernelRequestSessionWarmer
 * @package Mmi\EventListener
 */
class KernelRequestSessionWarmer
{
    /**
     * @var Session
     */
    private $session;
    
    /**
     * KernelEventSubscriber constructor.
     *
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }
    
    /**
     * @param GetResponseEvent $event
     */
    public function warmUpSession(GetResponseEvent $event)
    {
        $this->session->start();
    }
}
