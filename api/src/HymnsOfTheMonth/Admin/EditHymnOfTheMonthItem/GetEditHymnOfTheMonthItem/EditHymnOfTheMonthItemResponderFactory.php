<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Admin\EditHymnOfTheMonthItem\GetEditHymnOfTheMonthItem;

use App\HymnsOfTheMonth\HymnOfTheMonthItemResult;
use App\Responder;
use App\RespondWithJson;
use App\RespondWithNotFound;
use Psr\Http\Message\ResponseFactoryInterface;

readonly class EditHymnOfTheMonthItemResponderFactory
{
    public function __construct(private ResponseFactoryInterface $factory)
    {
    }

    public function create(HymnOfTheMonthItemResult $result): Responder
    {
        if (! $result->hasHymnOfTheMonthItem) {
            return new RespondWithNotFound(
                factory: $this->factory,
                message: 'Hymn of the month item not found',
            );
        }

        return new RespondWithJson(
            entity: $result->hymnOfTheMonthItem,
            factory: $this->factory,
        );
    }
}
