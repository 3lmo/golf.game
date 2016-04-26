<?php

namespace Quiz\BasicBundle\Handler;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Custom authentication success handler
 */
class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface {

	private $router;
	private $serviceContainer;

	public function __construct($router, $serviceContainer) {
		$this->router = $router;
		$this->serviceContainer = $serviceContainer;
	}

	function onAuthenticationSuccess(Request $request, TokenInterface $token) {
		$session = $this->serviceContainer->get('session');

		$session->set('loggedInWith', 'regularAccount');

		$redirectResponse = new RedirectResponse($this->router->generate('_home', array(), TRUE));
		return $redirectResponse;
	}

}