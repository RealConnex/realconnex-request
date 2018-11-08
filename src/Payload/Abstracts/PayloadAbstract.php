<?php

declare(strict_types=1);

namespace Realconnex\Payload\Abstracts;

abstract class PayloadAbstract
{
    protected $method;

    protected $key;

    public function getKey(): string
    {
        return $this->key;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
