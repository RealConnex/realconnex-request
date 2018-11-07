<?php

declare(strict_types=1);

namespace Realconnex;

class Schema
{
    private $schema = 'http://';

    public function useHttp()
    {
        $this->schema = 'http://';
    }

    public function useHttps()
    {
        $this->schema = 'https://';
    }

    public function getSchema(): string
    {
        return $this->schema;
    }
}
