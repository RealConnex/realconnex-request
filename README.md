# realconnex-request
Common library to make request

#### Installation

```bash
$ composer require realconnex/http-request
```
#### Configuration
Register class as a service in service.yml
```yaml
parameters:
    # flag indicates verification of hosts certificates
    verifyHost: '%env(bool:VERIFY_HOST)%'
    # web services names configuration
    webServices:
        green: '%env(string:SERVICE_DOMAIN_GREEN)%'
        blue: '%env(string:SERVICE_DOMAIN_BLUE)%'
        mc: '%env(string:SERVICE_DOMAIN_MC)%'
        feed: '%env(string:SERVICE_DOMAIN_FEED)%'
        mbau: '%env(string:SERVICE_DOMAIN_MBA)%'
        search: '%env(string:SERVICE_DOMAIN_SEARCH)%'
        email: '%env(string:SERVICE_DOMAIN_EMAIL)%'
        fapi: '%env(string:SERVICE_DOMAIN_FAPI)%'
        file: '%env(string:SERVICE_DOMAIN_FILE)%'
services:
    Realconnex\HttpRequest:
        arguments:
            $webServices: '%webServices%'
            $verifyHost: '%verifyHost%'
        public: true
```
#### Usage
Inject package into you class
```php
public function __construct(HttpRequest $httpRequest)
{
    $this->httpRequest = $httpRequest;
}
```
Send request
```php
$response = $this->httpRequest->sendRequest(
    HttpServices::MC, // service you want to reach
    'api/v1/notifications', // uri
    HttpRequest::METHOD_POST, // method
    $payload // payload
);
```
#### Options
- `verifyHost` – flag allows to set Guzzle client “verify” option for SSL-certificates verification.
- `webServices` – specifies a list of web services in format “name:domain” which this service can work with.
- `processExceptions` – flag indicates if exceptions should be processed automatically.
- `parseJson` – flag specifies that response should be automatically parsed as JSON.
- `authToken` – JWT token string for header Authorization.
- `provideAuth` – flag allows to specify should service use Authorization header or not (could be used in chain of microservices).
