<?php

declare(strict_types=1);

namespace App\MailingLists\Check;

use RuntimeException;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\Message;

/**
 * Thin wrapper over a connected webklex {@see Client}, exposing only the folder
 * operations the mailing-list check needs. Forwarding logic talks to this
 * object so it never touches the IMAP library directly.
 */
readonly class ImapMailbox
{
    private const string INBOX_FOLDER  = 'INBOX';
    private const string DRAFTS_FOLDER = 'Drafts';

    public function __construct(
        private Client $client,
        private IncomingMailFactory $incomingMailFactory,
    ) {
    }

    /**
     * Every message currently in the INBOX. The mailbox is dedicated to the
     * list, so all of it is forwarded and then removed — matching the legacy
     * Craft behavior. Messages are fetched without being flagged as read.
     */
    public function incomingMail(): IncomingMailCollection
    {
        $inbox = $this->client->getFolder(self::INBOX_FOLDER);

        if ($inbox === null) {
            throw new RuntimeException('Unable to open the INBOX folder');
        }

        $items = [];

        foreach ($inbox->query()->whereAll()->leaveUnread()->get() as $message) {
            if (! $message instanceof Message) {
                continue;
            }

            $items[] = $this->incomingMailFactory->fromMessage(message: $message);
        }

        return new IncomingMailCollection(items: $items);
    }

    public function moveToDrafts(IncomingMail $incomingMail): void
    {
        $incomingMail->message->move(self::DRAFTS_FOLDER);
    }

    public function delete(IncomingMail $incomingMail): void
    {
        $incomingMail->message->delete();
    }

    public function disconnect(): void
    {
        $this->client->disconnect();
    }
}
