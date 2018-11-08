<?php

declare(strict_types=1);

namespace Realconnex;

use GuzzleHttp\Psr7\Response;

class Reply
{
    /** @var bool */
    private $parse = true;
    /** @var bool */
    private $associative = true;

    /**
     * @param Response $response
     * @return array|object
     */
    public function parse(Response $response)
    {
        return json_decode($response->getBody()->getContents(), $this->associative);
    }

    public function setParse(bool $parse): void
    {
        $this->parse = $parse;
    }

    public function setAssociative(bool $associative): void
    {
        $this->associative = $associative;
    }

    public function isParsed(): bool
    {
        return $this->parse;
    }

    public function isAssociative():bool
    {
        return $this->associative;
    }
}
