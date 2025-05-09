<?php

declare(strict_types=1);

namespace Config\Events;

use Crell\Tukio\OrderedProviderInterface;

class EventSubscribers
{
    public static function register(OrderedProviderInterface $provider): void
    {
        $provider->addSubscriber(ApplyRoutes::class);
        $provider->addSubscriber(ApplyCommands::class);
    }
}
