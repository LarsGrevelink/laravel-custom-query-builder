<?php

namespace LGrevelink\CustomQueryBuilder\Exceptions;

use Exception;

class QueryBuilderException extends Exception
{
    /**
     * Constructor.
     *
     * @param string $message
     * @param Exception $previous
     * @param int $code
     */
    public function __construct($message = null, Exception $previous = null, $code = 0)
    {
        parent::__construct($message, $code, $previous);
    }
}
