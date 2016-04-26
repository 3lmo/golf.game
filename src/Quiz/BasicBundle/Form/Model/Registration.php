<?php

namespace Quiz\BasicBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Quiz\BasicBundle\Entity\User;

class Registration
{
	/**
	 * @Assert\Type(type="Quiz\BasicBundle\Entity\User")
	 * @Assert\Valid()
	 */
	protected $user;

	public function setUser(User $user)
	{
		$this->user = $user;
	}

	public function getUser()
	{
		return $this->user;
	}
}