<?php

declare(strict_types=1);

use Symfony\Component\HttpFoundation\RequestStack;
use Realconnex\Exceptions\NonExistentServiceException;

/**
 * Class HttpRequestTest
 */
class HttpRequestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Checks that default schema is 'http://'
     */
    public function testDefaultSchema()
    {
        $request = new \Realconnex\HttpRequest([], new RequestStack(), true, true, false);

        $this->assertEquals($request->getSchema(), 'http://');
    }

    /**
     * Checks that default option 'parseJsonAssoc' is true
     */
    public function testDefaultParseJsonAssocIsTrue()
    {
        $request = new \Realconnex\HttpRequest([], new RequestStack(), true, true, false);

        $this->assertTrue($request->getParseJsonAssoc());
    }

    /**
     * Checks that exception will be thrown when unexpected be required
     * @throws \Exception
     */
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
