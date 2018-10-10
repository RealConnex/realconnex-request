# realconnex-request
Common library to make request

### Installation

```bash
$ composer require realconnex/http-request
```
Register class as a service in service.yml
```yaml
parameters:
    # flag indicates verification of hosts certificates
    verifyHost: '%env(bool:VERIFY_HOST)%'
    # hostname of application frontend
    frontendHostName: '%env(FRONTEND_HOSTNAME)%'
    # web services names configuration
    webServices:
        green: 'green-rcx'
        blue: 'blue-rcx'
        mc: 'mc-rcx'
        feed: 'feed-rcx'
        mbau: 'mba-rcx'
        search: 'search-rcx'
        email: 'email-rcx'
        fapi: 'fapi-rcx'
        file: 'file-rcx'
services:
    Realconnex\HttpRequest:
        arguments:
            $webServices: '%webServices%'
            $verifyHost: '%verifyHost%'
            $parseJson: '%httpService.parseJson%'
            $provideAuth: '%httpService.provideAuth%'
        public: true
```
