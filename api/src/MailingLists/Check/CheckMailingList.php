<?php

declare(strict_types=1);

namespace App\MailingLists\Check;

use App\MailingLists\MailingList;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Connects to a single list's mailbox and forwards everything in its INBOX.
 * Failures are logged and swallowed so one misbehaving mailbox can't stop the
 * other lists from being processed.
 */
readonly class CheckMailingList
{
    public function __construct(
        private ImapClientFactory $imapClientFactory,
        private IncomingMailHandler $incomingMailHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(MailingList $mailingList): void
    {
        try {
            $mailbox = $this->imapClientFactory->connect(
                mailingList: $mailingList,
            );
        } catch (Throwable $error) {
            $this->logError(mailingList: $mailingList, error: $error);

            return;
        }

        try {
            $mailbox->incomingMail()->map(function (
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
        } catch (Throwable $error) {
            $this->logError(mailingList: $mailingList, error: $error);
        } finally {
            $mailbox->disconnect();
        }
    }

    private function logError(MailingList $mailingList, Throwable $error): void
    {
        $this->logger->error(
            'Mailing list check failed for ' . $mailingList->listAddress,
            [
                'mailingListId' => $mailingList->id->toString(),
                'message' => $error->getMessage(),
                'file' => $error->getFile(),
                'line' => $error->getLine(),
            ],
        );
    }
}
