<?php

namespace Quiz\BasicBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="g_statistic")
 * @ORM\Entity()
 */
class Statistic
{
	/**
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id()
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(name="game_type", type="string", length=30)
	 */
	private $game_type;

	/**
	 * @ORM\Column(name="correct", type="integer")
	 */
	private $correct;
	
	/**
	 * @ORM\Column(name="wrong", type="integer")
	 */
	private $wrong;

	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="statistics")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	private $user;

	public function __construct()
	{
		$this->users = new ArrayCollection();
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
     * Set game_type
     *
     * @param string $game_type
     */
    public function setGameType($game_type)
    {
        $this->game_type = $game_type;
    }

    /**
     * Get game_type
     *
     * @return string 
     */
    public function getGameType()
    {
        return $this->game_type;
    }
    
    /**
     * Set correct
     *
     * @param string $game_type
     */
    public function setCorrect($correct)
    {
    	$this->correct = $correct;
    }
    
    /**
     * Get correct
     *
     * @return string
     */
    public function getCorrect()
    {
    	return $this->correct;
    }
    
    /**
     * Set wrong
     *
     * @param string $wrong
     */
    public function setWrong($wrong)
    {
    	$this->wrong = $wrong;
    }
    
    /**
     * Get wrong
     *
     * @return string
     */
    public function getWrong()
    {
    	return $this->wrong;
    }

    /**
     * Set user
     *
     * @param \Quiz\BasicBundle\Entity\User $users
     */
    public function setUser(\Quiz\BasicBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return \Quiz\BasicBundle\Entity\User $users
     */
    public function getUser()
    {
        return $this->user;
    }
}
