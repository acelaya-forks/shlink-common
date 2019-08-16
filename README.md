# Shlink Common

This library provides some utils and conventions for web apps. It's main purpose is to be used on [Shlink](https://github.com/shlinkio/shlink) project, but any PHP project can take advantage.

Most of the elements it provides require a [PSR-11](https://www.php-fig.org/psr/psr-11/) container, and it's easy to integrate on [expressive](https://github.com/zendframework/zend-expressive) applications thanks to the `ConfigProvider` it includes.

[![Build Status](https://img.shields.io/travis/shlinkio/shlink-common.svg?style=flat-square)](https://travis-ci.org/shlinkio/shlink-common)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/shlinkio/shlink-common.svg?style=flat-square)](https://scrutinizer-ci.com/g/shlinkio/shlink-common/?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/shlinkio/shlink-common.svg?style=flat-square)](https://scrutinizer-ci.com/g/shlinkio/shlink-common/?branch=master)
[![Latest Stable Version](https://img.shields.io/github/release/shlinkio/shlink-common.svg?style=flat-square)](https://packagist.org/packages/shlinkio/shlink-common)
[![License](https://img.shields.io/github/license/shlinkio/shlink-common.svg?style=flat-square)](https://github.com/shlinkio/shlink-common/blob/master/LICENSE)
[![Paypal donate](https://img.shields.io/badge/Donate-paypal-blue.svg?style=flat-square&logo=paypal&colorA=aaaaaa)](https://acel.me/donate)

## Install

Install this library using composer:

    composer require shlinkio/shlink-common

> This library is also an expressive module which provides its own `ConfigProvider`. Add it to your configuration to get everything automatically set up.

## Cache

A [doctrine cache](https://www.doctrine-project.org/projects/doctrine-cache/en/1.8/index.html) adapter is registered, which returns different instances depending on your configuration:
 
 * An `ArrayCache` instance when the `debug` config is set to true or when the APUc extension is not installed and the `cache.redis` config is not defined.
 * An `ApcuCache`instance when no `cache.redis` is defined and the APCu extension is installed.
 * A `PredisCache` instance when the `cache.redis` config is defined.
 
 Any of the adapters will use the namespace defined in `cache.namespace` config entry.
 
 ```php
<?php
declare(strict_types=1);

return [

    'debug' => false,

    'cache' => [
        'namespace' => 'my_namespace',
        'redis' => [
            'servers' => [
                'tcp://1.1.1.1:6379',
                'tcp://2.2.2.2:6379',
                'tcp://3.3.3.3:6379',
            ],
        ],
    ],

];
```

When the `cache.redis` config is provided, a set of servers is expected. If only one server is provided, this library will treat it as a regular server, but if several servers are defined, it will treat them as a redis cluster and expect the servers to be configured as such.

## Middlewares

This module provides a set of useful middlewares, all registered as services in the container:

* `CloseDatabaseConnectionMiddleware`:

    Should be an early middleware in the pipeline. It makes use of the EntityManager that ensure the database connection is closed at the end of the request.

    It should be used when serving an app with a non-blocking IO server (like Swoole or ReactPHP), which persist services between requests.

* `LocaleMiddleware`:

    Sets the locale in the translator, based on the `Accapt-Language` header.

* `IpAddress` (from [akrabat/ip-address-middleware](https://github.com/akrabat/ip-address-middleware) package):

    Improves detection of the remote IP address.

    The set of headers which are inspected in order to search for the address can be customized using this configuration:

    ```php
    <?php
    declare(strict_types=1);

    return [

        'ip_address_resolution' => [
            'headers_to_inspect' => [
                'CF-Connecting-IP',
                'True-Client-IP',
                'X-Real-IP',
                'Forwarded',
                'X-Forwarded-For',
                'X-Forwarded',
                'X-Cluster-Client-Ip',
                'Client-Ip',
            ],
        ],

    ];
    ```

## Doctrine integration

Some doctrine-related services are provided, that can be customized via configuration:

### EntityManager

The EntityManager service can be fetched using names `em` or `Doctrine\ORM\EntityManager`.

In any case, it will come decorated so that it is reopened automatically after having been closed.

The EntityManager can be customized using this configuration:

```php
<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Common;

return [

    'entity_manager' => [
        'orm' => [
            'proxies_dir' => 'data/proxies', // Directory in which proxies will be persisted
            'entities_mappings' => [ // List of directories from which entities mappings should be read
                __DIR__ . '/../foo/entities-mappings',
                __DIR__ . '/../bar/entities-mappings',
            ],
            'types' => [ // List of custom database types to map
                Doctrine\Type\ChronosDateTimeType::CHRONOS_DATETIME => Doctrine\Type\ChronosDateTimeType::class,
            ],
        ],
        'connection' => [ // Database connection params
            'driver' => 'pdo_mysql',
            'host' => 'shlink_db',
            'user' => 'DB_USER',
            'password' => 'DB_PASSWORD',
            'dbname' => 'DB_NAME',
            'charset' => 'utf8',
        ],
    ],

];
```

### Connections

As well as the EntityManager, there are two Connection objects that can be fetched.

* `Doctrine\DBAL\Connection`: Returns the connection used by the EntityManager, as is.
* `Shlinkio\Shlink\Common\Doctrine\NoDbNameConnection`: Returns a connection which is the same used by the EntityManager but without setting the database name. Useful to perform operations like creating the database (which would otherwise fail since the database does not exist yet).

## Logger

*TODO*

## I18n

*TODO*

## Utils

* `DottedAccessConfigAbstractFactory`: A zend-servicemanager abstract factory that lets any config param to be fetched as a service by using the `config.foo.bar` notation.