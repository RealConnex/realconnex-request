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

    const HEADER_AUTH_TOKEN = 'Authorization';

    /** @var bool flag indicates if exceptions should be processed automatically */
    private $processExceptions = true;
    /** @var bool */
    private $verifyHost = true;
    /** @var string */
    private $authToken;
    /** @var bool */
    private $provideAuth = false;
    /** @var Schema */
    private $schema;
    /** @var Reply */
    private $reply;
    /** @var Payload */
    private $payload;
    /** @var Services */
    private $services;

    /**
     * HttpService constructor.
     * @param array $webServices
     * @param RequestStack $requestStack
     */
    public function __construct(array $webServices, RequestStack $requestStack)
    {
        $this->schema = new Schema();
        $this->reply = new Reply();
        $this->payload = new Payload();
        $this->services = new Services($webServices);
        $currentRequest = $requestStack->getCurrentRequest();
        $this->authToken = !empty($currentRequest) ? $currentRequest->headers->get(self::HEADER_AUTH_TOKEN) : null;
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
        $authToken = $this->getAuthToken();
        if (!empty($authToken) && $this->getProvideAuth()) {
            $headers[self::HEADER_AUTH_TOKEN] = $authToken;
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

//    /**
//     * @param string $method
//     * @throws WrongHttpMethod
//     */
//    private function checkMethodType(string $method): void
//    {
//         if (!in_array(strtolower($method), [self::METHOD_POST, self::METHOD_PUT, self::METHOD_GET, self::METHOD_DELETE])) {
//             throw new WrongHttpMethod($method);
//         }
//    }

//    private function preparePayload(string $method, array $data): array
//    {
//        $request = [];
//        if ($method === self::METHOD_GET) {
//            $request[RequestOptions::QUERY] = $data['data'];
//        }
//            if (!empty($data['files'])) { // If multipartData is not empty, we are sending files.
//                $request[RequestOptions::MULTIPART] = $data['files'];
//            } else {
//                $request[RequestOptions::JSON] = $data['data'];
//            }
//
//        return $request;
//    }


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

    public function setAuthToken(?string $authToken): self
    {
        $this->authToken = $authToken;

        return $this;
    }

    public function setProvideAuth(bool $provideAuth): self
    {
        $this->provideAuth = $provideAuth;

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
    public function getAuthToken(): ?string
    {
        return $this->authToken;
    }

    public function getProvideAuth(): bool
    {
        return $this->provideAuth;
    }

    public function getProcessExceptions(): bool
    {
        return $this->processExceptions;
    }
}
