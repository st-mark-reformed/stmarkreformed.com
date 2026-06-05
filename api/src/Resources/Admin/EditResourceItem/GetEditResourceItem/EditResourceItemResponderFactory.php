<?php

declare(strict_types=1);

namespace App\Resources\Admin\EditResourceItem\GetEditResourceItem;

use App\Resources\ResourceItemResult;
use App\Responder;
use App\RespondWithJson;
use App\RespondWithNotFound;
use Psr\Http\Message\ResponseFactoryInterface;

readonly class EditResourceItemResponderFactory
{
    public function __construct(private ResponseFactoryInterface $factory)
    {
    }

    public function create(ResourceItemResult $result): Responder
    {
        if (! $result->hasResourceItem) {
            return new RespondWithNotFound(
                factory: $this->factory,
                message: 'Resource not found',
            );
        }

        return new RespondWithJson(
            entity: $result->resourceItem,
            factory: $this->factory,
        );
    }
}
