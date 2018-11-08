<?php

declare(strict_types=1);

namespace Realconnex;

use Symfony\Component\HttpFoundation\RequestStack;

class Auth
{
    /** @var string */
    const HEADER_AUTH_TOKEN = 'Authorization';

    /** @var string */
    private $token = '';

    /** @var bool */
    private $authorize = false;

    public function __construct(RequestStack $request)
    {
        $this->token = (string)$request->getCurrentRequest()->headers->get(self::HEADER_AUTH_TOKEN) ?? '';
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function authorize(): bool
    {
        return $this->authorize;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function setAuthorize(bool $authorize): void
    {
        $this->authorize = $authorize;
    }
}
