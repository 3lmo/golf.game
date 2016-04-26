<?php

namespace Quiz\BasicBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Quiz\BasicBundle\Form\Type\UserType;
use Quiz\BasicBundle\Entity\User;
use Quiz\BasicBundle\Form\Type\RegistrationType;
use Quiz\BasicBundle\Form\Model\Registration;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecuredController extends Controller {
	
	public function indexAction() {
		$logger = $this->get("logger");
		$logger->info("SecuredController::indexAction() started");	
		$request = $this->getRequest();
		$session = $request->getSession();
	
		$session->remove("loggedInWith");
		$form = $this->createForm(new RegistrationType(), new Registration());	
	
		if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
			$error = $this->get('translator')->trans($request->attributes->get(
					SecurityContext::AUTHENTICATION_ERROR)
			);
		} else {
			$error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
			$session->remove(SecurityContext::AUTHENTICATION_ERROR);
		}
	
		if ($this->getUser()) {
			return $this->redirect($this->generateUrl('_home'));
		} else {
			return $this->render(
				'QuizBasicBundle:Account:index.html.twig',
				array(
					'form' => $form->createView(),
					'last_username' => $session->get(SecurityContext::LAST_USERNAME), // last username entered by the user
					'error' => $error
				)
			);
		}	
	}
	
	public function registerAction() {
		$logger = $this->get("logger");
		/* @var $user_service \Quiz\BasicBundle\Service\UserService */
		$user_service = $this->get("user_service");
		$request = $this->getRequest();
		$session = $request->getSession();
		
		$logger->info("SecuredController::registerAction() started");
	
		$form = $this->createForm(new RegistrationType(), new Registration());
		$form->bind($request);
	
		if ($form->isValid()) {
			$logger->info("registerAction(): form is valid");
			$result = $user_service->createUser($form);
			
			if (isset($result["user"])) {
				$session->set('loggedInWith', 'systemAccount');
				//redirect logged in user
				return $this->redirect($this->generateUrl('_home'));
			}
			else {
				//display errors
				$logger->info("registerAction(): error on creation");
				return $this->render(
					'QuizBasicBundle:Account:index.html.twig', 
					array(
						'form' => $form->createView(),
						'last_username' => $session->get(SecurityContext::LAST_USERNAME), // last username entered by the user
						'error_register' => $result["error"]
					)
				);				
			}		
		} else {
			$logger->err("registerAction(): form is not valid");
			$logger->err($form->getErrorsAsString());
			return $this->render(
				'QuizBasicBundle:Account:index.html.twig', 
				array(
					'form' => $form->createView(),
					'last_username' => $session->get(SecurityContext::LAST_USERNAME), // last username entered by the user
					'errors' => $form->getErrors()
				)
			);
		}
	}
	
	public function anonymousLoginAction() {
		/* @var $user_service \Quiz\BasicBundle\Service\UserService */
		$user_service = $this->get("user_service");

		$this->get('session')->set('loggedInWith', 'anonymousAccount');
		
		//redirect logged in user
		return $this->redirect($this->generateUrl('_home'));
	}
	
	public function loginCheckAction() {
		$logger = $this->get("logger");
		$logger->info("SecuredController::loginCheckAction() started");
	}
}