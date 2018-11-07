<?php

declare(strict_types=1);

namespace Realconnex\Payload\Abstracts;

interface PayloadInterface
{
    public function getPayload(string $method, array $data): array;
}
