# E-POSTBUSINESS Provider for OAuth 2.0 Client

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]]()
[![Dependency Status][ico-dependencies]][link-dependencies]

This package provides E-POSTBUSINESS API OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Install

Via Composer

``` bash
$ composer require richardhj/oauth2-epost
```

## Usage

The provider supports _Authorization Code Grant_ as well as _Resource Owner Password Credentials Grant_. I recommend reading these usage instructions before: https://github.com/thephpleague/oauth2-client#usage
But instead of the `GenericProvider` you're going to use this provider.

This is how to initiate the provider:
```php
$provider = new Richardhj\EPost\OAuth2\Client\Provider\EPost(
    [
        'clientId'              => sprintf('%s,%s', EPOST_DEV_ID, EPOST_APP_ID),
        'redirectUri'           => 'http://localhost:8080/oauth2_redirect.php', // Only necessary for the Authorization Code Grant flow
        'scopes'                => ['create_letter', 'send_hybrid'],
        'lif'                   => EPOST_LIF_CONTENT,
        'enableTestEnvironment' => true,
    ]
);
```

## License

The  GNU Lesser General Public License (LGPL).

[ico-version]: https://img.shields.io/packagist/v/richardhj/oauth2-epost.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-LGPL-brightgreen.svg?style=flat-square
[ico-dependencies]: https://www.versioneye.com/php/richardhj:oauth2-epost/badge.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/richardhj/oauth2-epost
[link-dependencies]: https://www.versioneye.com/php/richardhj:oauth2-epost
