<?php

declare(strict_types=1);

namespace Realconnex;

class Schema
{
    /** @var string */
    private $schema = 'http://';

    public function useHttp(): void
    {
        $this->schema = 'http://';
    }

    public function useHttps(): void
    {
        $this->schema = 'https://';
    }

    public function getSchema(): string
    {
        return $this->schema;
    }
}
