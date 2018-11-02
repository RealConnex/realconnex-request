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
    # hostname of application frontend
    frontendHostName: '%env(FRONTEND_HOSTNAME)%'
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
    $this->httpRequest = $request;
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
