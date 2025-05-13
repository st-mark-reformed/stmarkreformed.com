<?php

declare(strict_types=1);

namespace Cli\Events;

use Crell\Tukio\OrderedProviderInterface;

readonly class EventSubscribers
{
    public static function register(OrderedProviderInterface $provider): void
    {
        $provider->addSubscriber(ApplyCliCommandsEventSubscriber::class);
    }
}
