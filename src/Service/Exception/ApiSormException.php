<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: ApiSormException.php
 * Updated At: 21.10.2024, 12:07
 *
 */

namespace SormModule\Service\Exception;

final class ApiSormException extends \Exception
{
    /**
     * Constructor ApiError object
     *
     * @param int        $code     - error code
     * @param string     $message  - error message
     * @param \Exception $previous - previous error
     */
    public function __construct(int $code, string $message, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Format error to string
     *
     * @return string
     */
    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}