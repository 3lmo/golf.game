<?php

namespace Quiz\BasicBundle\Service;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;

class DatabaseManager {
	
	private $em_local;
	
	private $em_remote;
	
	private $logger;

	private $members;

	public function __construct(EntityManager $em_local, EntityManager $em_remote, $members) {
		$this->em_local = $em_local;
		$this->em_remote = $em_remote;
		$this->members = $members;
		$this->logger = LogFactory::getLogger("DatabaseManager");
	}
	
	public function getAllPlayerIds() {
		$connection = $this->em_remote->getConnection();

		/*$query = "SELECT m.member_id, m.gender FROM `northstar-training`.mm_members m
				  LEFT JOIN `northstar-training`.cmn_images i
				  ON m.member_id = i.reference_id
				  WHERE m.member_status_id = 1
				  AND m.member_number IN (?)
				  AND i.image IS NOT NULL
				  AND ((m.member_type_id >= 1 AND m.member_type_id <= 19)
				  OR (m.member_type_id >= 36 AND m.member_type_id <= 82)
				  OR m.member_type_id IN (109,110,113,114,115,116,117,118,129,151))
				  ORDER BY m.last_name;";*/
		
		$query = "SELECT m.member_id, m.gender FROM `northstar-training`.mm_members m
				  LEFT JOIN `northstar-training`.cmn_images i
				  ON m.member_id = i.reference_id
				  WHERE m.member_status_id = 1
				  AND m.member_number IN (?)
				  AND i.image IS NOT NULL
				  ORDER BY m.last_name;";

		$statement = $connection->executeQuery($query,
			array($this->members),
			array(Connection::PARAM_INT_ARRAY));
		$result = $statement->fetchAll();
		
		return $result;
	}
		
	public function getPlayerImages($player_ids) {
		$connection = $this->em_remote->getConnection();
		$player_string = implode(",", $player_ids);
		$this->logger->info($player_string);
		
		$query = "SELECT i.reference_id, i.image FROM `northstar-training`.cmn_images i
				  WHERE i.reference_id IN (" . $player_string . ")";
		
		$statement = $connection->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		
		return $result;
	}
	
	public function getPlayerNames($player_ids) {
		$connection = $this->em_remote->getConnection();
		$player_string = implode(",", $player_ids);
		$this->logger->info($player_string);
		
		$query = "SELECT m.member_id, m.first_name, m.last_name, m.gender, m.prefix FROM `northstar-training`.mm_members m
				  WHERE m.member_id IN (" . $player_string . ");";
		
		$statement = $connection->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		
		return $result;
	}
	
}