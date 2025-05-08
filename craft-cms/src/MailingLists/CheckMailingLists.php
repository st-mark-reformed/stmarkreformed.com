<?php

declare(strict_types=1);

namespace App\MailingLists;

use App\Shared\FieldHandlers\Generic\GenericHandler;
use BuzzingPixel\CraftScheduler\Frequency;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItem;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItemCollection;
use craft\elements\GlobalSet;
use craft\errors\InvalidFieldException;
use craft\services\Globals;
use verbb\supertable\elements\db\SuperTableBlockQuery;
use verbb\supertable\elements\SuperTableBlockElement;

use function array_map;
use function assert;
use function is_array;

class CheckMailingLists
{
    public static function addSchedule(
        ScheduleConfigItemCollection $schedule,
    ): void {
        $schedule->addItem(new ScheduleConfigItem(
            className: self::class,
            runEvery: Frequency::ALWAYS,
        ));
    }

    public function __construct(
        private Globals $globals,
        private GenericHandler $genericHandler,
        private CheckMailingList $checkMailingList,
    ) {
    }

    /**
     * @throws InvalidFieldException
     */
    public function __invoke(): void
    {
        $mailingListsSet = $this->globals->getSetByHandle(
            'mailingLists',
        );

        assert($mailingListsSet instanceof GlobalSet);

        $mailingListsQuery = $mailingListsSet->getFieldValue(
            'mailingLists',
        );

        assert($mailingListsQuery instanceof SuperTableBlockQuery);

        $mailingListsArray = array_map(
            function (
                SuperTableBlockElement $listBlock,
            ): MailingList {
                $subscribers = $listBlock->getFieldValue(
                    'subscribers',
                );

                assert(is_array($subscribers));

                return new MailingList(
                    listName: $this->genericHandler->getString(
                        element: $listBlock,
                        field: 'listName',
                    ),
                    listAddress: $this->genericHandler->getString(
                        element: $listBlock,
                        field: 'listAddress',
                    ),
                    userName: $this->genericHandler->getString(
                        element: $listBlock,
                        field: 'usernameField',
                    ),
                    password: $this->genericHandler->getString(
                        element: $listBlock,
                        field: 'password',
                    ),
                    server: $this->genericHandler->getString(
                        element: $listBlock,
                        field: 'server',
                    ),
                    port: $this->genericHandler->getInt(
                        element: $listBlock,
                        field: 'port',
                    ),
                    connectionType: $this->genericHandler->getString(
                        element: $listBlock,
                        field: 'connectionType',
                    ),
                    subscribers: new SubscriberCollection(
                        array_map(
                            static function (
                                array $subscriber,
                            ): Subscriber {
                                return new Subscriber(
                                    name: (string) $subscriber['name'],
                                    emailAddress: (string) $subscriber['emailAddress'],
                                );
                            },
                            $subscribers,
                        )
                    ),
                );
            },
            $mailingListsQuery->all(),
        );

        $mailingLists = new MailingListCollection(items: $mailingListsArray);

        $mailingLists->map($this->checkMailingList);
    }
}
