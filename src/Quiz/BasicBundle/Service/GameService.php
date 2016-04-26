<?php

namespace Quiz\BasicBundle\Service;

use Doctrine\ORM\EntityManager;
use Quiz\BasicBundle\Service\DatabaseManager;

class GameService {

	private $logger;

	private $em;
	
	private $db_manager;

	private $prefix_mapping = array(
		'1' => 'Mr. ',
		'2' => 'Mrs. ',
		'3' => 'Mrs. ',
		'30' =>'Ms. ',
		'5' => 'Dr. ',
		'33' => 'Mrs. '
	);

	public function __construct(EntityManager $em, DatabaseManager $db_manager) {
		$this->logger = LogFactory::getLogger("user_service");
		$this->em = $em;
		$this->db_manager = $db_manager;
	}
	
	public function get10Random($data) {
		$random_numbers = array();
		
		while (count($random_numbers) < 10) {
			$random_key = array_rand($data, 1);
			$random_numbers[] = $data[$random_key]["member_id"];
			unset($data[$random_key]);
		}
		
		return array("users" => $random_numbers, "data" => $data);
	}
	
	public function getNRandom($data, $gender, $count) {
		$this->logger->info("target " . $gender . ": " . $count);
		$random_numbers = array();
		$genders = array("male" => 0, "female" => 0);
	
		while (count($random_numbers) < $count) {
			if ($gender) {
				$found = "";
				$this->logger->info($gender);
				while ($found != $gender) {
					$random_key = array_rand($data, 1);
					$found = $data[$random_key]["gender"];
					$this->logger->info("found: " . $found);
				}
			}
			else {
				$random_key = array_rand($data, 1);
			}			 
			
			$random_numbers[] = $data[$random_key]["member_id"];
			if ($data[$random_key]["gender"] == "Male") {
				$genders["male"]++;
			}
			else {
				$genders["female"]++;
			}
			unset($data[$random_key]);
			$this->logger->info("found numbers: " . count($random_numbers));
		}
		
		$genders["male"] *= 2;
		$genders["female"] *= 2;
	
		return array("users" => $random_numbers, "data" => $data, "genders" => $genders);
	}
	
	public function prepareEasyGame($correct, $option_female, $option_male) {		
		$player_images = $this->db_manager->getPlayerImages( $correct );
		$player_names = $this->db_manager->getPlayerNames( array_merge($correct, $option_female, $option_male) );
		$game["type"] = "easy";
		$gender = "";
		
		for ( $i=0; $i<10; $i++) {
			//player image
			foreach ($player_images as $player_image) {
				if ($player_image["reference_id"] == $correct[$i]) {
					$game["question"][$i]["image"] = base64_encode($player_image["image"]);
					break;
				}
			}
			//player names
			foreach ($player_names as $player_name) {
				if ($player_name["member_id"] == $correct[$i]) {					
					$gender = $player_name["gender"];
					$game["question"][$i]["order"][0]["name"] = $this->getSalutation($player_name["prefix"]) . $player_name["first_name"] . " " . $player_name["last_name"];
					$game["question"][$i]["order"][0]["id"] = intval($player_name["member_id"]);
					$game["question"][$i]["correct"]["name"] = $this->getSalutation($player_name["prefix"]) . $player_name["first_name"] . " " . $player_name["last_name"];
					$game["question"][$i]["correct"]["id"] = intval($player_name["member_id"]);
					break;					
				}
							
			}
			
			$index = 1;
			if ($gender == "Male") {
				$option1 = array_pop($option_male);
				$option2 = array_pop($option_male);
			}
			else {
				$option1 = array_pop($option_female);
				$option2 = array_pop($option_female);
			}
			
			foreach ($player_names as $player_name) {
				if ( $player_name["member_id"] == $option1 ) {
					$game["question"][$i]["order"][$index]["name"] = $this->getSalutation($player_name["prefix"]) . $player_name["first_name"] . " " . $player_name["last_name"];
					$game["question"][$i]["order"][$index]["id"] = intval($player_name["member_id"]);
					$index++;
				}
				if ( $player_name["member_id"] == $option2 ) {
					$game["question"][$i]["order"][$index]["name"] = $this->getSalutation($player_name["prefix"]) . $player_name["first_name"] . " " . $player_name["last_name"];
					$game["question"][$i]["order"][$index]["id"] = intval($player_name["member_id"]);
					$index++;
				}
				if ($index == 3) {
					break;
				}
			}
			
			//randomize
			shuffle($game["question"][$i]["order"]);			
		}
		
		return $game;
	}
	
	public function prepareMediumGame($correct) {	
		$player_images = $this->db_manager->getPlayerImages( $correct );
		$player_names = $this->db_manager->getPlayerNames( $correct );
		$game["type"] = "medium";
	
		for ( $i=0; $i<10; $i++) {
			//player image
			foreach ($player_images as $player_image) {
				if ($player_image["reference_id"] == $correct[$i]) {
					$game["question"][$i]["image"] = base64_encode($player_image["image"]);
					break;
				}
			}
			//player names
			foreach ($player_names as $player_name) {
				if ($player_name["member_id"] == $correct[$i]) {
					$this->logger->info("correct" . $i . ": " . $player_name["last_name"]);
					$game["question"][$i]["salutation"] = rtrim($this->getSalutation($player_name["prefix"]));
					$game["question"][$i]["correct"]["name"] = $player_name["last_name"];
					$game["question"][$i]["correct"]["id"] = intval($player_name["member_id"]);
				}
			}
		}
	
		return $game;
	}
	
	public function prepareHardGame($correct) {
		$player_images = $this->db_manager->getPlayerImages( $correct );
		$player_names = $this->db_manager->getPlayerNames( $correct );
		$game["type"] = "hard";
	
		for ( $i=0; $i<10; $i++) {
			//player image
			foreach ($player_images as $player_image) {
				if ($player_image["reference_id"] == $correct[$i]) {
					$game["question"][$i]["image"] = base64_encode($player_image["image"]);
					break;
				}
			}
			//player names
			foreach ($player_names as $player_name) {
				if ($player_name["member_id"] == $correct[$i]) {
					$game["question"][$i]["salutation"] = rtrim($this->getSalutation($player_name["prefix"]));
					$this->logger->info("correct" . $i . ": " . $player_name["first_name"]);
					$this->logger->info("correct" . $i . ": " . $player_name["last_name"]);
					$game["question"][$i]["correct"]["name"] = $player_name["first_name"] . " " . $player_name["last_name"];
					$game["question"][$i]["correct"]["first_name"] = $player_name["first_name"];
					$game["question"][$i]["correct"]["last_name"] = $player_name["last_name"];
					$game["question"][$i]["correct"]["id"] = intval($player_name["member_id"]);
				}
			}
		}
	
		return $game;
	}

	private function getSalutation($prefix) {
		$this->logger->info('prefix: ' . $prefix);
		$this->logger->info('salut: ' . $this->prefix_mapping[$prefix]);
		if (isset($this->prefix_mapping[$prefix])) {
			$salut = $this->prefix_mapping[$prefix];
		} else {
			$salut = '';
		}

		return $salut . ' ';
	}
}