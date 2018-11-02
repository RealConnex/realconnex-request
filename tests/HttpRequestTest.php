<?php

declare(strict_types=1);

use Symfony\Component\HttpFoundation\RequestStack;

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
}
