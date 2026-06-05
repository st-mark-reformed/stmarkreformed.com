<?php

declare(strict_types=1);

namespace App\MailingLists\Admin;

use App\MailingLists\Subscriber;
use App\MailingLists\Subscribers;
use RxAnte\AppBootstrap\Request\ServerRequest;

use function is_array;
use function is_string;
use function trim;

/**
 * Turns the repeating `subscribers[][name|emailAddress]` rows from an admin
 * create/edit request into a {@see Subscribers} collection, dropping rows with
 * no email address.
 */
readonly class SubscriberResolver
{
    public function resolve(ServerRequest $request): Subscribers
    {
        $raw = $request->parsedBody->attributes['subscribers'] ?? null;

        if (! is_array($raw)) {
            return new Subscribers();
        }

        $subscribers = [];

        foreach ($raw as $rawSubscriber) {
            if (! is_array($rawSubscriber)) {
                continue;
            }

            $emailAddress = trim($this->stringValue(
                value: $rawSubscriber['emailAddress'] ?? null,
            ));

            if ($emailAddress === '') {
                continue;
            }

            $subscribers[] = new Subscriber(
                name: trim($this->stringValue(
                    value: $rawSubscriber['name'] ?? null,
                )),
                emailAddress: $emailAddress,
            );
        }

        return new Subscribers(items: $subscribers);
    }

    private function stringValue(mixed $value): string
    {
        return is_string($value) ? $value : '';
    }
}
