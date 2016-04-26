<?php

namespace Quiz\BasicBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Routing\Router;
use Monolog\Logger;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    private $router;
    
    private $security_context;
    
    private $logger;

    public function __construct(SecurityContext $security_context, Router $router, Logger $logger)
    {
        $this->router 			= 	$router;
        $this->security_context = 	$security_context;
        $this->logger 			= 	$logger;
    }

    /**
     * Do the magic.
     *
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        /*$this->logger->info(var_export($event->getAuthenticationToken()->getRoles(), true));
        $this->logger->info(var_export($event->getAuthenticationToken()->isAuthenticated(), true));*/
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
    	$session = $event->getRequest()->getSession();
    	$this->logger->info($session->get("loggedInWith"));
    	if (!$this->security_context->getToken()->getUser() && $session->get("loggedInWith") != "anonymousAccount") {
    		$this->logger->info("LoginListener: no-user");
    		$url = $this->router->generate("_landing");
    		$event->setResponse(new RedirectResponse($url));
    	}
    }
}