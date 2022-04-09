<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\UpcomingEvents;

use App\Http\PageBuilder\BlockResponse\BlockResponseBuilderContract;
use App\Http\PageBuilder\BlockResponse\UpcomingEvents\Entities\UpcomingEventsContentModel;
use craft\elements\MatrixBlock;
use Twig\Environment as TwigEnvironment;

class UpcomingEvents implements BlockResponseBuilderContract
{
    public function __construct(
        private TwigEnvironment $twig,
        private UpcomingEventsRetriever $upcomingEventsRetriever,
    ) {
    }

    public function buildResponse(MatrixBlock $matrixBlock): string
    {
        return $this->twig->render(
            '@app/Http/PageBuilder/BlockResponse/UpcomingEvents/UpcomingEvents.twig',
            [
                'contentModel' => new UpcomingEventsContentModel(
                    heading: (string) $matrixBlock->getFieldValue(
                        'heading',
                    ),
                    subHeading: (string) $matrixBlock->getFieldValue(
                        'subHeading',
                    ),
                    events: $this->upcomingEventsRetriever->retrieve(),
                ),
            ],
        );
    }
}
