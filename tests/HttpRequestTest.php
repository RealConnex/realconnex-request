<?php

declare(strict_types=1);

use Realconnex\Auth;
use Realconnex\HttpRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Realconnex\Exceptions\NonExistentServiceException;

class HttpRequestTest extends \PHPUnit\Framework\TestCase
{
   /** @var RequestStack */
    private $stack;

    protected function setUp()
    {
        $this->stack = new RequestStack();
        $request = new Request();
        $request->headers->set(Auth::HEADER_AUTH_TOKEN, 'some token here');
        $this->stack->push($request);
    }


    public function testDefaultSchema()
    {
        $request = new HttpRequest([], $this->stack);

        $this->assertEquals($request->getSchema(), 'http://');
    }

    public function testChangeSchemaToHttps()
    {
        $request = new HttpRequest([], $this->stack);
        $request->useHttps();

        $this->assertEquals($request->getSchema(), 'https://');
    }

    public function testDoubleChangeSchemaToHttps()
    {
        $request = (new HttpRequest([], $this->stack))->useHttps();
        $request->useHttp();

        $this->assertEquals($request->getSchema(), 'http://');
    }

    public function testChangeParseResponse()
    {
        $request = (new HttpRequest([], $this->stack))
            ->setParseJson(false);

        $this->assertFalse($request->getParseJson());
    }

    public function testChangeParseResponseAssociative()
    {
        $request = (new HttpRequest([], $this->stack))
            ->setParseJsonAssoc(false);

        $this->assertFalse($request->getParseJsonAssoc());
    }

    public function testDefaultParseJsonAssocIsTrue()
    {
        $request = new HttpRequest([], $this->stack);

        $this->assertTrue($request->getParseJsonAssoc());
    }

    public function testWrongServiceException()
    {
        $request = new HttpRequest(['blue' => 'blue.rcx.l'], $this->stack);

        $this->expectException(NonExistentServiceException::class);

        $request->sendRequest(
            'NonExistentService',
            'api/v1/test',
            HttpRequest::METHOD_GET
        );
    }

//    public function testWrongHttpMethodException()
//    {
//        $request = new HttpRequest(['blue' => 'blue.rcx.l'], new RequestStack());
//
//        $this->expectException(WrongHttpMethod::class);
//
//        $request->sendRequest(
//            HttpServices::BLUE,
//            'api/v1/test',
//            'wrong method'
//        );
//    }

    public function testRightServiceException()
    {
        $request = (new HttpRequest(['blue' => 'api.kit.dev.lebedev-studio.com'], $this->stack))
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
