<?php

declare(strict_types=1);

namespace Realconnex;

use Realconnex\Exceptions\NonExistentServiceException;

class Services
{
    /** @var array */
    private $services = [];

    public function __construct(array $services)
    {
        foreach ($services as $name => $url) {
            $this->addService($name, $url);
        }
    }

    public function addService(string $name, string $url): void
    {
        $this->services[$name] = $url;
    }

    public function getService(string $name): string
    {
        $this->checkService($name);
        return $this->services[$name];
    }

    /**
     * @param string $name
     * @return bool
     * @throws NonExistentServiceException
     */
    public function checkService(string $name): bool
    {
        if (!isset($this->services[$name])) {
            throw  new NonExistentServiceException($name);
        }

        return true;
    }
}
