<?php

declare(strict_types=1);

namespace App\Profiles\SetHasMessages;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use craft\elements\Entry;
use craft\errors\ElementNotFoundException;
use craft\errors\InvalidFieldException;
use craft\services\Elements as ElementsService;
use Throwable;
use yii\base\Exception;

class SetHasMessagesOnAProfile
{
    public function __construct(
        private GenericHandler $genericHandler,
        private EntryQueryFactory $queryFactory,
        private ElementsService $elementsService,
    ) {
    }

    /**
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws InvalidFieldException
     * @throws Exception
     */
    public function set(Entry $profile): void
    {
        $query = $this->queryFactory->make();

        $query->section('messages');

        $query->relatedTo([
            'targetElement' => $profile,
            'field' => 'profile',
        ]);

        $hasMessages = $query->count() > 0;

        $existingValue = $this->genericHandler->getBoolean(
            element: $profile,
            field: 'hasMessages',
        );

        if ($hasMessages === $existingValue) {
            return;
        }

        $profile->setFieldValue(
            'hasMessages',
            $hasMessages
        );

        $this->elementsService->saveElement($profile);
    }
}
