<?php

declare(strict_types=1);

namespace Config\Dependencies;

use Config\RuntimeConfigOptions;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Psr\Container\ContainerInterface;
use RxAnte\AppBootstrap\Dependencies\Bindings;
use RxAnte\AppBootstrap\RuntimeConfig;

use function explode;

readonly class ElasticSearch
{
    public function __invoke(Bindings $bindings): void
    {
        $bindings->addBinding(
            Client::class,
            static function (ContainerInterface $di): Client {
                $runtimeConfig = $di->get(RuntimeConfig::class);

                $hosts = $runtimeConfig->getString(
                    RuntimeConfigOptions::ELASTIC_SEARCH_HOSTS,
                );

                return ClientBuilder::create()
                    ->setHosts(explode(',', $hosts))
                    ->build();
            },
        );
    }
}
