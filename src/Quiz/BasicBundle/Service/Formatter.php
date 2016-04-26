<?php

use Quiz\BasicBundle\Service\LogFactory;
use Symfony\Component\Form\Form;
class Formatter {
	
	private $logger;
	
	public function __construct() {
		$this->logger = LogFactory::getLogger("formatter");
	}
	
	public static function getErrorMessages(Form $form) {
		$errors = array();
		foreach ($form->getErrors() as $key => $error) {
			$template = $error->getMessageTemplate();
			$parameters = $error->getMessageParameters();
	
			foreach ($parameters as $var => $value) {
				$template = str_replace($var, $value, $template);
			}
	
			$errors[$key] = $template;
		}
		
		return $errors;
	}
}