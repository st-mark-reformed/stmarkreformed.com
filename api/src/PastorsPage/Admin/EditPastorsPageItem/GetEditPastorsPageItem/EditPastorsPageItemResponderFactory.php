<?php

declare(strict_types=1);

namespace App\PastorsPage\Admin\EditPastorsPageItem\GetEditPastorsPageItem;

use App\PastorsPage\PastorsPageItemResult;
use App\Responder;
use App\RespondWithJson;
use App\RespondWithNotFound;
use Psr\Http\Message\ResponseFactoryInterface;

readonly class EditPastorsPageItemResponderFactory
{
    public function __construct(private ResponseFactoryInterface $factory)
    {
    }

    public function create(PastorsPageItemResult $result): Responder
    {
        if (! $result->hasPastorsPageItem) {
            return new RespondWithNotFound(
                factory: $this->factory,
                message: 'Pastors page item not found',
            );
        }

        return new RespondWithJson(
            entity: $result->pastorsPageItem,
            factory: $this->factory,
        );
    }
}
