<?php

namespace Quiz\BasicBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Quiz\BasicBundle\Entity\Statistic;

class DefaultController extends Controller
{
    public function homeAction()
    {
    	$logger = $this->get("logger");
    	/* @var $db_manager \Quiz\BasicBundle\Service\DatabaseManager */
    	$db_manager = $this->get("db_manager");
    	$logger->info("DefaultController::homeAction() started");
    	
        return $this->render(
        			'QuizBasicBundle:Default:home.html.twig',
        			array(
        				'game_category' => $this->container->getParameter("game_category")
        			)
        		);
    }
    
    public function easyGameAction () {
    	$logger = $this->get("logger");
    	/* @var $db_manager \Quiz\BasicBundle\Service\DatabaseManager */
    	$db_manager = $this->get("db_manager");
    	/* @var $game_service \Quiz\BasicBundle\Service\GameService */
    	$game_service = $this->get("game_service");
    	$logger->info("DefaultController::easyGameAction() started");
    	
    	$player_ids = $db_manager->getAllPlayerIds();
		$logger->info('found: ' . count($player_ids) . ' players');
    	$result_correct = $game_service->getNRandom($player_ids, false, 10);
    	if ($result_correct["genders"]["female"] > 0 && $result_correct["genders"]["male"] > 0) {
    		$result_female = $game_service->getNRandom($result_correct["data"], "Female", $result_correct["genders"]["female"]);
    		$result_male = $game_service->getNRandom($result_female["data"], "Male", $result_correct["genders"]["male"]);
    	}
    	elseif ($result_correct["genders"]["female"] > 0 && $result_correct["genders"]["male"] == 0) {
    		$result_female = $game_service->getNRandom($result_correct["data"], "Female", $result_correct["genders"]["female"]);
    		$result_male["users"] = array();
    	}
    	elseif ($result_correct["genders"]["female"] == 0 && $result_correct["genders"]["male"] > 0) {
    		$result_male = $game_service->getNRandom($result_correct["data"], "Male", $result_correct["genders"]["male"]);
    		$result_female["users"] = array();
    	}
    	
    	
    	$game = $game_service->prepareEasyGame($result_correct["users"], $result_female["users"], $result_male["users"]);    	
    	
    	return $this->render(
	    			'QuizBasicBundle:Default:game.html.twig',
	    			array(
	    				'game' => $game
	    			)
    			);
    }
    
    public function mediumGameAction () {
    	$logger = $this->get("logger");
    	/* @var $db_manager \Quiz\BasicBundle\Service\DatabaseManager */
    	$db_manager = $this->get("db_manager");
    	/* @var $game_service \Quiz\BasicBundle\Service\GameService */
    	$game_service = $this->get("game_service");
    	$logger->info("DefaultController::mediumGameAction() started");
    	
    	$player_ids = $db_manager->getAllPlayerIds();
    	$result_correct = $game_service->get10Random($player_ids);
    	 
    	$game = $game_service->prepareMediumGame($result_correct["users"]);
    	 
    	return $this->render(
    			'QuizBasicBundle:Default:game.html.twig',
    			array(
    					'game' => $game
    			)
    	);
    }
    
    public function hardGameAction () {
    	$logger = $this->get("logger");
    	/* @var $db_manager \Quiz\BasicBundle\Service\DatabaseManager */
    	$db_manager = $this->get("db_manager");
    	/* @var $game_service \Quiz\BasicBundle\Service\GameService */
    	$game_service = $this->get("game_service");
    	$logger->info("DefaultController::hardGameAction() started");
    	
    	$player_ids = $db_manager->getAllPlayerIds();
    	$result_correct = $game_service->get10Random($player_ids);
    	
    	$game = $game_service->prepareHardGame($result_correct["users"]);
    	
    	return $this->render(
    			'QuizBasicBundle:Default:game.html.twig',
    			array(
    					'game' => $game
    			)
    	);
    }
    
    public function endGameAction () {
    	$logger = $this->get("logger");
    	$em = $this->getDoctrine()->getManager();
    	$request = $this->getRequest();
    	
    	if ($this->getUser()) {
    		$user = $em->getRepository("QuizBasicBundle:User")->find($this->getUser()->getId());
    		
    		if ($user) {
    			$stats = json_decode($request->get("stats"), true);
    			 
    			$statistic = new Statistic();
    			$statistic->setGameType($stats["type"]);
    			$statistic->setCorrect($stats["correct"]);
    			$statistic->setWrong($stats["wrong"]);
    			$statistic->setUser($user);
    			$user->addStatistic($statistic);
    			
    			try {
    				$em->persist($user);
    				$em->flush();
    			}catch (\PDOException $e) {
    				$logger->err($e->getMessage());
    				$logger->err($e->getTraceAsString());
    			}
    		}   		
    	}	
    	
    	return new Response(json_encode($request->get("stats")));
    }
}
