<?php

declare(strict_types=1);

namespace Realconnex\Exceptions;

class NonExistentServiceException extends \Exception
{
    public function __construct(string $service)
    {
        $this->message = sprintf('Call to non existent service %s', $service);
        $this->code = 1000;

        parent::__construct($this->message, $this->code, null);
    }
}
