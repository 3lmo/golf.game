<?php

namespace Quiz\BasicBundle\Listener;

use Monolog\Logger;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\FlattenException;

class ExceptionListener{
    
    private $logger =null;


    /**
     * @param null|Monolog\Logger $logger
     */
    public function __construct(Logger $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if($this->logger === null)
            return;

        $exception = $event->getException();
        $flattenException = FlattenException::create($exception);
        $this->logger->err('Stack trace');
        $this->logger->err($flattenException->getMessage());
        foreach ($flattenException->getTrace() as $trace) {
            $traceMessage = sprintf('  at %s line %s', $trace['file'], $trace['line']);
            $this->logger->err($traceMessage);
        }
    }
}