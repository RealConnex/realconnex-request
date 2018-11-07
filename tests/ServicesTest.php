<?php

declare(strict_types=1);

use Realconnex\Exceptions\NonExistentServiceException;
use Realconnex\Services;

class ServicesTest extends \PHPUnit\Framework\TestCase
{
    /** @var Services */
    protected $services;

    protected function setUp()
    {
        $this->services = new Services([]);
    }

    public function testCheckServiceHasNoKeyException()
    {
        $this->services->addService('test', 'test_uri');
        $this->expectException(NonExistentServiceException::class);

        $this->services->checkService('test_1');
    }

    public function testCheckServiceHasKey()
    {
        $this->services->addService('test', 'test_uri');

        $this->assertTrue($this->services->checkService('test'));
    }

    public function testGetServiceHasNoKeyException()
    {
        $this->services->addService('test', 'test_uri');
        $this->expectException(NonExistentServiceException::class);

        $this->services->getService('test_1');
    }

}
