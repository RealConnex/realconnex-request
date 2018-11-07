<?php

declare(strict_types=1);

namespace Realconnex\Payload\Abstracts;

abstract class PayloadAbstract
{
    protected $method;

    protected $key;

//    public function getPayload(string $method, array $data): array
//    {
//        $payload = [];
//        if ($this->method = $method && !empty($data)) {
//            $payload = [$this->getKey() => $data];
//        }
//
//        return $payload;
//    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
