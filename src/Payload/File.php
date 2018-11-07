<?php

declare(strict_types=1);

namespace Realconnex\Payload;

use GuzzleHttp\RequestOptions;
use Realconnex\Payload\Abstracts\PayloadAbstract;
use Realconnex\Payload\Abstracts\PayloadInterface;

class File extends PayloadAbstract implements PayloadInterface
{
    protected $method = 'post';

    protected $key = RequestOptions::MULTIPART;

    public function getPayload(string $method, array $data): array
    {
        $payload = [];
        if ($this->method === $method && !empty($data['files'])) {
            $payload = [$this->getKey() => $data['files']];
        }

        return $payload;
    }
}
