<?php

declare(strict_types=1);

namespace App\Series\Admin\EditSeries\GetEditSeries;

use App\Responder;
use App\RespondWithJson;
use App\RespondWithNotFound;
use App\Series\SeriesResult;
use Psr\Http\Message\ResponseFactoryInterface;

readonly class EditSeriesResponderFactory
{
    public function __construct(private ResponseFactoryInterface $factory)
    {
    }

    public function create(SeriesResult $result): Responder
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
