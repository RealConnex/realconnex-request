<?php

declare(strict_types=1);

use Realconnex\Exceptions\WrongHttpMethod;
use Realconnex\HttpRequest;
use Realconnex\HttpServices;
use Symfony\Component\HttpFoundation\RequestStack;
use Realconnex\Exceptions\NonExistentServiceException;

class HttpRequestTest extends \PHPUnit\Framework\TestCase
{
    public function testDefaultSchema()
    {
        $request = new HttpRequest([], new RequestStack());

        $this->assertEquals($request->getSchema(), 'http://');
    }

    public function testChangeSchemaToHttps()
    {
        $request = new HttpRequest([], new RequestStack());
        $request->useHttps();

        $this->assertEquals($request->getSchema(), 'https://');
    }

    public function testDoubleChangeSchemaToHttps()
    {
        $request = (new HttpRequest([], new RequestStack()))
            ->useHttps();
        $request->useHttp();

        $this->assertEquals($request->getSchema(), 'http://');
    }

    public function testChangeParseResponse()
    {
        $request = (new HttpRequest([], new RequestStack()))
            ->setParseJson(false);

        $this->assertFalse($request->getParseJson());
    }

    public function testChangeParseResponseAssociative()
    {
        $request = (new HttpRequest([], new RequestStack()))
            ->setParseJsonAssoc(false);

        $this->assertFalse($request->getParseJsonAssoc());
    }

    public function testDefaultParseJsonAssocIsTrue()
    {
        $request = new HttpRequest([], new RequestStack());

        $this->assertTrue($request->getParseJsonAssoc());
    }

    public function testWrongServiceException()
    {
        $request = new HttpRequest(['blue' => 'blue.rcx.l'], new RequestStack());

        $this->expectException(NonExistentServiceException::class);

        $request->sendRequest(
            'NonExistentService',
            'api/v1/test',
            HttpRequest::METHOD_GET
        );
    }

    public function testWrongHttpMethodException()
    {
//        $request = new HttpRequest(['blue' => 'blue.rcx.l'], new RequestStack());
//
//        $this->expectException(WrongHttpMethod::class);
//
//        $request->sendRequest(
//            HttpServices::BLUE,
//            'api/v1/test',
//            'wrong method'
//        );
    }

    public function testRightServiceException()
    {
        $request = (new HttpRequest(['blue' => 'api.kit.dev.lebedev-studio.com'], new RequestStack()))
                ->useHttp()
                ->setParseJson(true);

        $reply = $request->sendRequest(
            'blue',
            'v1/auth/verify/code',
            HttpRequest::METHOD_POST,
            [
                'phone' => 70000000000,
                'code' => "0000"
            ]
        );

        $this->assertTrue(true);
    }
}
