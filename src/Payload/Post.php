<?php

declare(strict_types=1);

namespace Realconnex\Payload;

use GuzzleHttp\RequestOptions;
use Realconnex\Payload\Abstracts\PayloadAbstract;
use Realconnex\Payload\Abstracts\PayloadInterface;

class Post extends PayloadAbstract implements PayloadInterface
{
    protected $method = 'post';

    protected $key = RequestOptions::JSON;

    public function getPayload(string $method, array $data): array
    {
        $payload = [];
        if ($this->method === $method && !empty($data['data'])) {
            $payload = [$this->getKey() => $data['data']];
        }

        return $payload;
    }

}
