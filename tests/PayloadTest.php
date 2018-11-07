<?php

declare(strict_types=1);

use Realconnex\Payload;
use Realconnex\Payload\File;
use Realconnex\Payload\Get;
use Realconnex\Payload\Post;
use Realconnex\Payload\Put;

class PayloadTest extends \PHPUnit\Framework\TestCase
{
    /** @var Payload */
    protected $payload;

    protected function setUp()
    {
        $this->payload = new Payload();
    }

    public function testReturnKeysForPut()
    {
        $put = new Put();
        $reply = $this->payload->getPayload('put', [
            'data' => [
                'a' => 'b'
            ]
        ]);

        $this->assertArrayHasKey($put->getKey(), $reply);
    }

    public function testReturnKeysForFiles()
    {
        $file = new File();
        $reply = $this->payload->getPayload('post', [
            'files' => [
                'a' => 'b'
            ]
        ]);

        $this->assertArrayHasKey($file->getKey(), $reply);
    }

    public function testReturnKeysForGet()
    {
        $get = new Get();
        $reply = $this->payload->getPayload('get', [
            'data' => [
                'a' => 'b'
            ]
        ]);

        $this->assertArrayHasKey($get->getKey(), $reply);
    }

    public function testReturnKeysForPost()
    {
        $post = new Post();
        $reply = $this->payload->getPayload('post', [
            'data' => [
                'a' => 'b'
            ]
        ]);

        $this->assertArrayHasKey($post->getKey(), $reply);
    }
}
