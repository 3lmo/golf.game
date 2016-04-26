<?php

namespace Acme\HelloBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\HelloBundle\Entity\User;
use Quiz\BasicBundle\Entity\Role;

class LoadRoleData implements FixtureInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$save = false;
		
		//check existing
		$role_user = $manager->getRepository("QuizBasicBundle:Role")->findOneBy(array("role" => "ROLE_USER"));
		if (!$role_user) {
			$role_user = new Role();
			$role_user->setName("ROLE_USER");
			$role_user->setRole("ROLE_USER");
			
			$manager->persist($role_user);
			$save = true;
		}
		
		$role_admin = $manager->getRepository("QuizBasicBundle:Role")->findOneBy(array("role" => "ROLE_ADMIN"));
		if (!$role_admin) {
			$role_admin = new Role();
			$role_admin->setName("ROLE_ADMIN");
			$role_admin->setRole("ROLE_ADMIN");
			
			$manager->persist($role_admin);
			$save = true;
		}		

		if ($save) {	
			$manager->flush();
		}		
	}
}