<?php

declare(strict_types=1);

use Realconnex\Auth;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AuthTest extends \PHPUnit\Framework\TestCase
{
    public function testTokenProvided()
    {
        $stack = new RequestStack();
        $request = new Request();
        $request->headers->set(Auth::HEADER_AUTH_TOKEN, 'some token here');
        $stack->push($request);
        $auth = new Auth($stack);

        $this->assertNotEmpty($auth->getToken());
    }

    public function testNoTokenProvided()
    {
        $stack = new RequestStack();
        $request = new Request();
        $stack->push($request);
        $auth = new Auth($stack);

        $this->assertEmpty($auth->getToken());
    }
}
