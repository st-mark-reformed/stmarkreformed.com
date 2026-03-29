<?php

declare(strict_types=1);

namespace App\Messages\Admin\EditMessage\GetEditMessage;

use App\Messages\MessageResult;
use App\Responder;
use App\RespondWithJson;
use App\RespondWithNotFound;
use Psr\Http\Message\ResponseFactoryInterface;

readonly class EditMessageResponderFactory
{
    public function __construct(private ResponseFactoryInterface $factory)
    {
    }

    public function create(MessageResult $result): Responder
    {
        if (! $result->hasMessage) {
            return new RespondWithNotFound(
                factory: $this->factory,
                message: 'Message not found',
            );
        }

        return new RespondWithJson(
            entity: $result->message,
            factory: $this->factory,
        );
    }
}
