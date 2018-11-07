<?php

declare(strict_types=1);

namespace Realconnex\Exceptions;

class WrongHttpMethod extends \Exception
{
    public function __construct(string $method)
    {
        $this->message = sprintf('Using undefined method %s', $method);
        $this->code = 1001;

        parent::__construct($this->message, $this->code, null);
    }
}
