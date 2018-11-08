<?php

declare(strict_types=1);

namespace Realconnex;

use Realconnex\Payload\Get;
use Realconnex\Payload\Post;
use Realconnex\Payload\File;
use Realconnex\Payload\Put;
use Realconnex\Payload\Abstracts\PayloadInterface;

class Payload
{
    /** @var array */
    private $payloads = [];

    public function __construct()
    {
        $this->addType(new Get());
        $this->addType(new Post());
        $this->addType(new File());
        $this->addType(new Put());
    }

    public function getPayload(string $method, array $data): array
    {
        $keys = [];
        /** @var PayloadInterface $payload */
        foreach ($this->payloads as $payload) {
            if (!empty($load = $payload->getPayload($method, $data))) {
                $keys = $load;
            }
        }

        return $keys;
    }

    private function addType(PayloadInterface $payload): void
    {
        $this->payloads[] = $payload;
    }
}
