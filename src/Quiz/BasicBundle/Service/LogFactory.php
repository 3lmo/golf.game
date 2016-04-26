<?php

namespace Quiz\BasicBundle\Service;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Quiz\BasicBundle\Handler\SessionRequestProcessor;

class LogFactory
{
	
	public static function getLogger($className)
	{				
		$logsDir = self::getLogsDirectory($_SERVER["SCRIPT_FILENAME"]);
		if (!$className)
		{
		  $logPath = $logsDir . "default.log";
		  $logChannel = "app";
		}		   
		else
		{
		  $logPath = $logsDir . $className . ".log";
		  $logChannel = "$className";
		}
		
		//create logger instace with corresponding log channel
		$logger = new Logger($logChannel);
		
		//push stream handler with log path and log level
		$streamHandler = new StreamHandler($logPath, 100);
		$logger->pushHandler($streamHandler);
		
		//push custom request proccesor to include session token in each log line
		$logger->pushProcessor((array(new SessionRequestProcessor(), 'processRecord')));
		
		return $logger;
	}
	
	/*
	 * 
	 * string $scriptDir path to executable script 
	 * 
	 * return string project root directory
	 */
	private static function getLogsDirectory($scriptDir)
	{
	  //call dirname recursively until document root is reached (means we reached project root directory)
 	  while (dirname($scriptDir) != $_SERVER["DOCUMENT_ROOT"])
 	  {
 	    $scriptDir = dirname($scriptDir);
 	  }
 	  
 	  return $scriptDir . "/app/logs/";
	}
}