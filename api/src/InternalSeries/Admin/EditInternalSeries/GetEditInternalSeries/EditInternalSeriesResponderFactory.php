<?php

declare(strict_types=1);

namespace App\InternalSeries\Admin\EditInternalSeries\GetEditInternalSeries;

use App\InternalSeries\InternalSeriesResult;
use App\Responder;
use App\RespondWithJson;
use App\RespondWithNotFound;
use Psr\Http\Message\ResponseFactoryInterface;

readonly class EditInternalSeriesResponderFactory
{
    public function __construct(private ResponseFactoryInterface $factory)
    {
    }

    public function create(InternalSeriesResult $result): Responder
    {
        if (! $result->hasSeries) {
            return new RespondWithNotFound(
                factory: $this->factory,
                message: 'Series not found',
            );
        }

        return new RespondWithJson(
            entity: $result->series,
            factory: $this->factory,
        );
    }
}
