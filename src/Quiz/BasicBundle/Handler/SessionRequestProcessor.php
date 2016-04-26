<?php

namespace Quiz\BasicBundle\Handler;

use Symfony\Component\HttpFoundation\Session\Session;
class SessionRequestProcessor
{
	private $session;
	private $token;

	public function __construct()
	{
		$this->session = $_SESSION;    
	}

	public function processRecord(array $record)
	{
		if (null === $this->token) {
			try {
				$this->token = substr(session_id(), 0, 8);
			} catch (\RuntimeException $e) {
				$this->token = 'no_token';
			}
			$this->token .= '-' . substr(uniqid(), -8);
		}
		$record['extra']['token'] = $this->token;

		return $record;
	}
}