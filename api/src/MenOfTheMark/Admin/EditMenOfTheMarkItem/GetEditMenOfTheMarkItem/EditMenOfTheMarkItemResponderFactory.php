<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Admin\EditMenOfTheMarkItem\GetEditMenOfTheMarkItem;

use App\MenOfTheMark\MenOfTheMarkItemResult;
use App\Responder;
use App\RespondWithJson;
use App\RespondWithNotFound;
use Psr\Http\Message\ResponseFactoryInterface;

readonly class EditMenOfTheMarkItemResponderFactory
{
    public function __construct(private ResponseFactoryInterface $factory)
    {
    }

    public function create(MenOfTheMarkItemResult $result): Responder
    {
        if (! $result->hasMenOfTheMarkItem) {
            return new RespondWithNotFound(
                factory: $this->factory,
                message: 'Men of the Mark item not found',
            );
        }

        return new RespondWithJson(
            entity: $result->menOfTheMarkItem,
            factory: $this->factory,
        );
    }
}
