<?php

namespace iDutch\CrossbarHttpBridge\Exception;

class RequestException extends \Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        $finalMessage = sprintf(
            'Error POSTing request to Crossbar: %s',
            $message
        );
        parent::__construct($finalMessage, $code, $previous);
    }
}
