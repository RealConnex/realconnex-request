<?php

declare(strict_types=1);

use Symfony\Component\HttpFoundation\RequestStack;
use Realconnex\Exceptions\NonExistentServiceException;

class HttpRequestTest extends \PHPUnit\Framework\TestCase
{
    public function testDefaultSchema()
    {
        $request = new \Realconnex\HttpRequest([], new RequestStack(), true, true, false);

        $this->assertEquals($request->getSchema(), 'http://');
    }

    public function testDefaultParseJsonAssocIsTrue()
    {
        $request = new \Realconnex\HttpRequest([], new RequestStack(), true, true, false);

        $this->assertTrue($request->getParseJsonAssoc());
    }

    public function testWrongServiceException()
    {
        $request = new \Realconnex\HttpRequest([], new RequestStack(), true, true, false);

        $this->expectException(NonExistentServiceException::class);

        $request->sendRequest(
            'NonExistentService',
            'api/v1/test',
            \Realconnex\HttpRequest::METHOD_GET
        );
    }
}
