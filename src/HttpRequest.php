<?php

declare(strict_types=1);

namespace Realconnex;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\HttpFoundation\RequestStack;

class HttpRequest
{
    const METHOD_GET = 'get';
    const METHOD_POST = 'post';
    const METHOD_PUT = 'put';
    const METHOD_DELETE = 'delete';

    /** @var bool flag indicates if exceptions should be processed automatically */
    private $processExceptions = true;
    /** @var bool */
    private $verifyHost = true;
    /** @var Schema */
    private $schema;
    /** @var Reply */
    private $reply;
    /** @var Payload */
    private $payload;
    /** @var Services */
    private $services;
    /** @var Auth */
    private $auth;

    /**
     * HttpService constructor.
     * @param array<string, string> $webServices
     * @param RequestStack $requestStack
     */
    public function __construct(array $services, RequestStack $requestStack)
    {
        $this->services = new Services($services);
        $this->auth = new Auth($requestStack);
        $this->schema = new Schema();
        $this->reply = new Reply();
        $this->payload = new Payload();
    }

    /**
     * Send request to service
     * @param string $service
     * @param string $url
     * @param string $method
     * @param array $data
     * @param array $files
     *
     * @return Reply|mixed
     *
     * @throws \Exception
     */
    public function sendRequest(string $service, string $url, string $method, array $data = [], array $files = [])
    {
        $data = [
            'data' => $data,
            'files' => $this->prepareFiles($files),
        ];
        $client = $this->prepareClient($service);
        $payload = $this->payload->getPayload($method, $data);

        try {
            /** @var Response $response */
            $response = $client->{$method}($url, $payload);
        } catch (BadResponseException $exception) {
            // If processing of exceptions is disabled, throw raw exception (should be processed manually)
            if (!$this->getProcessExceptions()) {
                throw new \Exception($exception->getResponse()->getBody()->getContents(), $exception->getCode());
            } else {
                throw $exception;
            }
        }

        if ($this->reply->isParsed()) {
            $response = $this->reply->parse($response);
        }

        return $response;
    }

    /**
     * Prepares request client.
     *
     * @param string $service
     * @return Client
     * @throws \Exception
     */
    private function prepareClient(string $service): Client
    {
        $this->services->checkService($service);
        $headers = [];
        // Send authorization token in the request
        if (!empty($token = $this->auth->getToken()) && $this->auth->authorize()) {
            $headers[$this->auth::HEADER_AUTH_TOKEN] = $token;
        }

        return new Client([
            'base_uri' => $this->schema->getSchema() . $this->services->getService($service) . '/',
            'verify'   => $this->verifyHost,
            'headers'  => $headers
        ]);
    }

    /**
     * Wraps files to array format with name, contents, filename
     * @param array $files
     *
     * @return array
     */
    private function prepareFiles(array $files): array
    {
        $multipartData = [];
        if (!empty($files)) {
            foreach ($files as $key => $file) {
                $multipartData[] = [
                    'name'     => "files[{$key}]",
                    'contents' => file_get_contents($file->getRealPath()),
                    'filename' => $file->getClientOriginalName()
                ];
            }
            if (!empty($data)) {
                foreach ($data as $keyName => $keyValue) {
                    $multipartData[] = [
                        'name'     => $keyName,
                        'contents' => $keyValue
                    ];
                }
            }
        }

        return $multipartData;
    }

    public function setParseJson(bool $parseJson): self
    {
        $this->reply->setParse($parseJson);

        return $this;
    }

    public function setParseJsonAssoc(bool $parseJsonAssoc): self
    {
        $this->reply->setAssociative($parseJsonAssoc);

        return $this;
    }

    public function useHttps(): self
    {
        $this->schema->useHttps();

        return $this;
    }

    public function useHttp(): self
    {
        $this->schema->useHttp();

        return $this;
    }

    public function setAuthToken(string $token): self
    {
        $this->auth->SetToken($token);

        return $this;
    }

    public function setProvideAuth(bool $provide): self
    {
        $this->auth->setAuthorize($provide);

        return $this;
    }

    public function setProcessExceptions(bool $processExceptions): self
    {
        $this->processExceptions = $processExceptions;

        return $this;
    }

    public function getParseJson(): bool
    {
        return $this->reply->isParsed();
    }

    public function getSchema(): string
    {
        return $this->schema->getSchema();
    }

    public function getParseJsonAssoc(): bool
    {
        return $this->reply->isAssociative();

    }

    public function getProcessExceptions(): bool
    {
        return $this->processExceptions;
    }
}
