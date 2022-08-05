<?php

declare(strict_types=1);

namespace App\MailingLists;

use Config\Paths;
use PhpImap\Exception;
use PhpImap\IncomingMail;
use PhpImap\Mailbox;

use function array_map;
use function implode;

class CheckMailingList
{
    public function __construct(
        private Paths $paths,
        private IncomingMailHandler $incomingMailHandler,
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(MailingList $mailingList): void
    {
        $imapPath = implode(
            '',
            [
                '{',
                $mailingList->server(),
                ':',
                $mailingList->port(),
                '/imap/',
                $mailingList->connectionType(),
                '}INBOX',
            ],
        );

        $mailbox = new Mailbox(
            imapPath: $imapPath,
            login: $mailingList->userName(),
            password: $mailingList->password(),
            attachmentsDir: $this->paths->imapAttachmentsPath(),
            serverEncoding: 'UTF-8',
        );

        $incomingMail = new IncomingMailCollection(items: array_map(
            static function (string | int $id) use (
                $mailbox,
            ): IncomingMail {
                return $mailbox->getMail($id);
            },
            $mailbox->searchMailbox(),
        ));

        $incomingMail->map(function (
            IncomingMail $incomingMail,
        ) use (
            $mailbox,
            $mailingList,
        ): void {
            $this->incomingMailHandler->handle(
                mailbox: $mailbox,
                mailingList: $mailingList,
                incomingMail: $incomingMail,
            );
        });
    }
}
