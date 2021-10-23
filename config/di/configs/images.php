<?php

declare(strict_types=1);

use App\Images\ImageCacheFileSystem;
use App\Images\ImageHandler;
use League\Flysystem\Adapter\Local;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\OptimizerChainFactory;

use function DI\autowire;

return [
    ImageCacheFileSystem::class => static function (): ImageCacheFileSystem {
        return new ImageCacheFileSystem(
            new Local(CRAFT_BASE_PATH . '/public/imagecache'),
        );
    },
    ImageHandler::class => autowire()->constructorParameter(
        'urlToImageCacheDirectory',
        '/imagecache',
    ),
    OptimizerChain::class => static function (ContainerInterface $di): OptimizerChain {
        $chain = OptimizerChainFactory::create();

        /** @psalm-suppress MixedArgument */
        $chain->useLogger($di->get(LoggerInterface::class));

        return OptimizerChainFactory::create();
    },
];
