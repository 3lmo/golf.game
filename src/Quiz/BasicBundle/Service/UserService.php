<?php

namespace Quiz\BasicBundle\Service;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Form;
use Quiz\BasicBundle\Entity\User;

class UserService {
	
	private $logger;
	
	private $request;
	
	private $em;
	
	public function __construct(Request $request, EntityManager $em) {
		$this->logger = LogFactory::getLogger("user_service");
		$this->request = $request;
		$this->em = $em;
	}
	
	public function createUser(Form $form) {		
		$form_user = $form->getData()->getUser();
		
		//encode password
		$salt = md5(uniqid(null, true));
		$MessageDigestPasswordEncoder = new MessageDigestPasswordEncoder('sha1', FALSE, 1);
		$encodedPassword = $MessageDigestPasswordEncoder->encodePassword($form_user->getPassword(), $salt);
		
		//check for existing user
		$existing_user = $this->em->getRepository("QuizBasicBundle:User")->findOneBy(array("username" => $form_user->getUsername()));
		if ($existing_user) {
			//existing user
			$result["error"] = "User with username " . $form_user->getUsername() . " already exists!";			
		}
		else {	
			//find role
			$user_role = $this->em->getRepository("QuizBasicBundle:Role")->findOneBy(array("role" => "ROLE_USER"));
			
			if (!$user_role) {
				//forgot to insert roles in db
				$result["error"] = "Cannot create basic user. No role! Try again later!";
			}
			else {
				try {
					$user = new User();
					
					//create user
					$user = $form_user
					->setIsActive(true)
					->setSalt($salt)
					->setPassword($encodedPassword)
					->addRole($user_role);
					
					//save
					$this->em->persist($user);
					$this->em->flush();
					
					$result["user"] = $user;
				}
				catch (\PDOException $e) {
					$this->logger->err($e->getMessage());
					$this->logger->err($e->getTraceAsString());
					//db error
					$result["error"] = "Database Error! Try again later!";
				}
			}						
		}
		
		return $result;
	}
	
	public function createDummyUser() {
		//encode password
		$salt = md5(uniqid(null, true));
		$password = uniqid(null, true);
		$MessageDigestPasswordEncoder = new MessageDigestPasswordEncoder('sha1', FALSE, 1);
		$encodedPassword = $MessageDigestPasswordEncoder->encodePassword($password, $salt);
		
		$user = new User();
		
		//$user_role = $this->em->getRepository("QuizBasicBundle:Role")->findOneBy(array("role" => "ROLE_USER"));
			
		//create user
		$user->setUsername("anonymous")
		->setIsActive(true)
		->setSalt($salt)
		->setPassword($encodedPassword);
		//->addRole($user_role);
		
		return $user;
	}
}