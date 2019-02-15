<?php

declare(strict_types=1);

namespace Realconnex;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Realconnex\Exceptions\NonExistentServiceException;
use Symfony\Component\HttpFoundation\RequestStack;

class HttpRequest
{
    const METHOD_GET = 'get';
    const METHOD_POST = 'post';
    const METHOD_PUT = 'put';
    const METHOD_DELETE = 'delete';

    const HEADER_AUTH_TOKEN = 'Authorization';
    const HEADER_REQUESTED_ENTITY_STRUCTURE = 'entity-structure';

    /** @var bool flag indicates if exceptions should be processed automatically */
    private $processExceptions = false;
    /** @var array */
    private $webServices;
    /** @var bool */
    private $verifyHost;
    /** @var bool */
    private $parseJson;
    /** @var bool */
    private $parseJsonAssoc = true;
    /** @var string */
    private $authToken;
    /** @var bool */
    private $provideAuth;
    /** @var string */
    private $schema = 'http://';
    /** @var array */
    private $options = [];
    /** @var \Realconnex\RequestStructure\EntityField[] */
    private $requestedEntityStructure;
    
    private $serializer;

    /**
     * HttpRequest constructor.
     * @param array $webServices
     * @param RequestStack $requestStack
     * @param bool $verifyHost
     * @param bool $parseJson
     * @param bool $provideAuth
     */
    public function __construct(
        array $webServices,
        RequestStack $requestStack,
        bool $verifyHost = true,
        bool $parseJson = true,
        bool $provideAuth = false
    ) {
        $this->webServices = $webServices;
        $this->verifyHost = $verifyHost;
        $this->parseJson = $parseJson;
        $this->provideAuth = $provideAuth;
        $currentRequest = $requestStack->getCurrentRequest();
        $this->authToken = !empty($currentRequest) ? $currentRequest->headers->get(self::HEADER_AUTH_TOKEN) : null;
    }
    /**
     * Get process exceptions
     * @return bool
     */
    public function getProcessExceptions(): bool
    {
        return $this->processExceptions;
    }
    /**
     * Set process exceptions
     * @param bool $processExceptions
     * @return HttpRequest
     */
    public function setProcessExceptions(bool $processExceptions): self
    {
        $this->processExceptions = $processExceptions;

        return $this;
    }
    /**
     * Get parse JSON flag
     * @return bool
     */
    public function getParseJson(): bool
    {
        return $this->parseJson;
    }
    /**
     * Set parse JSON flag
     * @param bool $parseJson
     * @return HttpRequest
     */
    public function setParseJson(bool $parseJson): self
    {
        $this->parseJson = $parseJson;

        return $this;
    }
    public function setParseJsonAssoc(bool $parseJsonAssoc): self
    {
        $this->parseJsonAssoc = $parseJsonAssoc;

        return $this;
    }
    /**
     * Get auth token
     */
    public function getAuthToken(): ?string
    {
        return $this->authToken;
    }
    /**
     * Set auth token
     * @param null|string $authToken
     * @return HttpRequest
     */
    public function setAuthToken(?string $authToken): self
    {
        $this->authToken = $authToken;

        return $this;
    }
    /**
     * Get provide auth flag
     * @return bool
     */
    public function getProvideAuth(): bool
    {
        return $this->provideAuth;
    }
    /**
     * Set provide auth flag
     * @param bool $provideAuth
     * @return HttpRequest
     */
    public function setProvideAuth(bool $provideAuth): self
    {
        $this->provideAuth = $provideAuth;

        return $this;
    }

    public function useHttps(): self
    {
        $this->schema = 'https://';

        return $this;
    }

    public function useHttp(): self
    {
       $this->schema = 'http://';

       return $this;
    }

    public function getSchema(): string
    {
        return $this->schema;
    }
    /**
     * Send request to service
     * @param string $service
     * @param string $url
     * @param string $method
     * @param array $data
     * @param array $files
     * @return Response|mixed
     * @throws \Exception
     */
    public function sendRequest(string $service, string $url, string $method, array $data = [], array $files = [])
    {
        $client = $this->prepareClient($service);
        // Wraps files to array format with name, contents, filename
        if (!empty($files)) {
            $multipartData = [];
            foreach ($files as $key => $file) {
                $multipartData[] = [
                    'name' => "files[{$key}]",
                    'contents' => file_get_contents($file->getRealPath()),
                    'filename' => $file->getClientOriginalName()
                ];
            }
            if (!empty($data)) {
                foreach ($data as $keyName => $keyValue) {
                    $multipartData[] = [
                        'name' => $keyName,
                        'contents' => $keyValue
                    ];
                }
            }
        }
        $requestData = $this->options;
        if ($method === self::METHOD_GET) {
            $requestData[RequestOptions::QUERY] = $data;
        } else {
            // If multipartData is not empty, we are sending files.
            if (!empty($multipartData)) {
                $requestData[RequestOptions::MULTIPART] = $multipartData;
            } else {
                $requestData[RequestOptions::JSON] = $data;
            }
        }
        try {
            /** @var Response $response */
            $response = $client->{$method}($url, $requestData);
        } catch (BadResponseException $exception) {
            // If processing of exceptions is disabled, throw raw exception (should be processed manually)
            if (!$this->getProcessExceptions()) {
                throw new \Exception($exception->getResponse()->getBody()->getContents(), $exception->getCode());
            } else {
                throw $exception;
            }
        }
        if ($this->getParseJson()) {
            return $this->parseJson($response);
        }

        return $response;
    }
    /**
     * @param Response $response
     * @return mixed
     */
    public function parseJson(Response $response)
    {
        return json_decode($response->getBody()->getContents(), $this->parseJsonAssoc);
    }

    public function getParseJsonAssoc(): bool
    {
        return $this->parseJsonAssoc;
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
        if (!in_array($service, array_keys($this->webServices))) {
            throw new NonExistentServiceException($service);
        }
        $headers = [];
        // Send authorization token in the request
        $authToken = $this->getAuthToken();
        if (!empty($authToken) && $this->getProvideAuth()) {
            $headers[self::HEADER_AUTH_TOKEN] = $authToken;
        }

        if (!empty($this->requestedEntityStructure)) {
            $headers[self::HEADER_REQUESTED_ENTITY_STRUCTURE] = json_encode($this->requestedEntityStructure);
        }

        return new Client([
            'base_uri' => $this->schema . $this->webServices[$service] . '/',
            'verify' => $this->verifyHost,
            'headers' => $headers
        ]);
    }

    public function setHeader($name, $value)
    {
        $this->options[RequestOptions::HEADERS][$name] = $value;
        return $this;
    }

    public function removeHeader($name)
    {
        if (isset($this->options[RequestOptions::HEADERS][$name])) {
            unset($this->options[RequestOptions::HEADERS][$name]);
        }

        return $this;
    }

    /**
     * @param \Realconnex\RequestStructure\EntityField[] $requestedStructure
     *
     * @return \Realconnex\HttpRequest
     */
    public function setRequestedStructure(array $requestedStructure)
    {
        $this->requestedEntityStructure = $requestedStructure;
        return $this;
    }
}
