<?php

declare(strict_types=1);

namespace Realconnex\Payload;

use GuzzleHttp\RequestOptions;
use Realconnex\Payload\Abstracts\PayloadAbstract;
use Realconnex\Payload\Abstracts\PayloadInterface;

class Get extends PayloadAbstract implements PayloadInterface
{
    protected $method = 'get';

    protected $key = RequestOptions::QUERY;

    public function getPayload(string $method, array $data): array
    {
        $payload = [];
        if ($this->method === $method && !empty($data['data'])) {
            $payload = [$this->getKey() => $data['data']];
        }

        return $payload;
    }
}
