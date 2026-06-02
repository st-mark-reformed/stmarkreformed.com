<?php

declare(strict_types=1);

namespace App\News\Admin\EditNewsItem\GetEditNewsItem;

use App\News\NewsItemResult;
use App\Responder;
use App\RespondWithJson;
use App\RespondWithNotFound;
use Psr\Http\Message\ResponseFactoryInterface;

readonly class EditNewsItemResponderFactory
{
    public function __construct(private ResponseFactoryInterface $factory)
    {
    }

    public function create(NewsItemResult $result): Responder
    {
        if (! $result->hasNewsItem) {
            return new RespondWithNotFound(
                factory: $this->factory,
                message: 'News item not found',
            );
        }

        return new RespondWithJson(
            entity: $result->newsItem,
            factory: $this->factory,
        );
    }
}
