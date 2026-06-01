<?php

declare(strict_types=1);

namespace App\InternalMessages\Admin\EditInternalMessage\GetEditInternalMessage;

use App\InternalMessages\InternalMessageResult;
use App\Responder;
use App\RespondWithJson;
use App\RespondWithNotFound;
use Psr\Http\Message\ResponseFactoryInterface;

readonly class EditInternalMessageResponderFactory
{
    public function __construct(private ResponseFactoryInterface $factory)
    {
    }

    public function create(InternalMessageResult $result): Responder
    {
        if (! $result->hasMessage) {
            return new RespondWithNotFound(
                factory: $this->factory,
                message: 'Internal message not found',
            );
        }

        return new RespondWithJson(
            entity: $result->message,
            factory: $this->factory,
        );
    }
}
