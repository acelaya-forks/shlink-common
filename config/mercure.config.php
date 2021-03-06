<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Common;

use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Lcobucci\JWT\Configuration;
use Symfony\Component\Mercure\Publisher;

return [

    'mercure' => [],

    'dependencies' => [
        'factories' => [
            Configuration::class => Mercure\JwtConfigFactory::class,
            Mercure\LcobucciJwtProvider::class => ConfigAbstractFactory::class,
            Publisher::class => Mercure\PublisherFactory::class,
        ],
    ],

    ConfigAbstractFactory::class => [
        Mercure\LcobucciJwtProvider::class => [Configuration::class, 'config.mercure'],
    ],

];
