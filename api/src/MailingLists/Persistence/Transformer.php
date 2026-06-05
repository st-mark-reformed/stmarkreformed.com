<?php

declare(strict_types=1);

namespace App\MailingLists\Persistence;

use App\MailingLists\ConnectionType;
use App\MailingLists\MailingList;
use App\MailingLists\MailingLists;
use App\MailingLists\Subscriber;
use App\MailingLists\Subscribers;
use Ramsey\Uuid\Uuid;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class Transformer
{
    public function toEntities(
        MailingListRecords $records,
        SubscriberRecords $subscriberRecords,
    ): MailingLists {
        $grouped = $this->groupSubscribersByListId($subscriberRecords);

        return new MailingLists(items: $records->map(
            fn (MailingListRecord $r) => $this->toEntity(
                record: $r,
                subscribers: $grouped[$r->id] ?? new Subscribers(),
            ),
        ));
    }

    public function toEntity(
        MailingListRecord $record,
        Subscribers $subscribers,
    ): MailingList {
        return new MailingList(
            id: Uuid::fromString($record->id),
            listName: $record->list_name,
            listAddress: $record->list_address,
            imapServer: $record->imap_server,
            imapPort: $record->imap_port,
            connectionType: ConnectionType::fromString($record->connection_type),
            username: $record->username,
            password: $record->password,
            subscribers: $subscribers,
        );
    }

    public function subscribersFromRecords(SubscriberRecords $records): Subscribers
    {
        return new Subscribers(items: $records->map(
            fn (SubscriberRecord $r) => $this->toSubscriber(record: $r),
        ));
    }

    private function toSubscriber(SubscriberRecord $record): Subscriber
    {
        return new Subscriber(
            id: Uuid::fromString($record->id),
            name: $record->name,
            emailAddress: $record->email_address,
        );
    }

    /** @return array<string, Subscribers> */
    private function groupSubscribersByListId(
        SubscriberRecords $subscriberRecords,
    ): array {
        $byListId = [];

        foreach ($subscriberRecords->records as $record) {
            $byListId[$record->mailing_list_id][] = $this->toSubscriber(
                record: $record,
            );
        }

        $grouped = [];

        foreach ($byListId as $listId => $subscribers) {
            $grouped[$listId] = new Subscribers(items: $subscribers);
        }

        return $grouped;
    }
}
