<?php
// src/Quiz/BasicBundle/Entity/User.php
namespace Quiz\BasicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Quiz\BasicBundle\Entity\User
 *
 * @ORM\Table(name="g_user")
 * @ORM\Entity(repositoryClass="Quiz\BasicBundle\Entity\UserRepository")
 */
class User implements UserInterface, \Serializable
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=25, unique=true)
	 */
	private $username;

	/**
	 * @ORM\Column(type="string", length=100)
	 * @Assert\NotBlank(message = "Username cannot be blank!")
	 */
	private $password;

	/**
	 * @ORM\Column(name="is_active", type="boolean")
	 * @Assert\NotBlank(message = "Password cannot be blank!")
	 */
	private $isActive;
	
	/**
	 * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
	 * @ORM\JoinTable(name="g_user_role")
	 * 
	 */
	private $roles;
	
	/**
	 * @ORM\Column(type="string", length=60)
	 */
	private $salt;
	
	/**
	 * @ORM\OneToMany(targetEntity="Statistic", mappedBy="user", cascade={"persist", "remove", "merge"})
	 **/
	private $statistics;

	public function __construct()
	{
		$this->isActive = true;
		$this->roles = new ArrayCollection();
		$this->statistics = new ArrayCollection();
		$this->salt = md5(uniqid(null, true));
	}

	/**
	 * @inheritDoc
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @inheritDoc
	 */
	public function getSalt()
	{
		return $this->salt;
	}

	/**
	 * @inheritDoc
	 */
	public function getPassword()
	{
		return $this->password;
	}

    public function getRoles()
    {
        return $this->roles->toArray();
    }
    
    /**
     * Get statistics
     *
     * @return \Quiz\BasicBundle\Entity\Statistic $statistics
     */
    public function getStatistics()
    {
    	return $this->statistics;
    }

	/**
	 * @inheritDoc
	 */
	public function eraseCredentials()
	{
	}

	/**
	 * @see \Serializable::serialize()
	 */
	public function serialize()
	{
		return serialize(array(
				$this->id,
				$this->username,
				$this->password,
				$this->salt,
		));
	}

	/**
	 * @see \Serializable::unserialize()
	 */
	public function unserialize($serialized)
	{
		list (
				$this->id,
				$this->username,
				$this->password,
				$this->salt
		) = unserialize($serialized);
	}

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Add roles
     *
     * @param \Quiz\BasicBundle\Entity\Role $roles
     * @return User
     */
    public function addRole(\Quiz\BasicBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \Quiz\BasicBundle\Entity\Role $roles
     */
    public function removeRole(\Quiz\BasicBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }
    
    /**
     * Add statistics
     *
     * @param \Quiz\BasicBundle\Entity\Statistic $statistics
     */
    public function addStatistic(\Quiz\BasicBundle\Entity\Statistic $statistics)
    {
    	$this->statistics[] = $statistics;
    }
    
    /**
     * Remove statistics
     *
     * @param \Quiz\BasicBundle\Entity\Statistic $statistics
     */
    public function removeStatistic(\Quiz\BasicBundle\Entity\Statistic $statistics)
    {
    	$this->statistics->removeElement($statistics);
    }
}
