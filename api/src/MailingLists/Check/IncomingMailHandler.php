<?php

declare(strict_types=1);

namespace App\MailingLists\Check;

use App\MailingLists\MailingList;
use Config\SystemFromAddress;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Throwable;

use function array_map;
use function array_merge;
use function array_values;
use function in_array;
use function preg_match;
use function preg_quote;
use function preg_replace;
use function strtolower;
use function trim;

/**
 * Forwards a single incoming list message to the list's subscribers, applying
 * the same rules as the legacy Craft mailing list: the list's system address
 * sends on behalf of the original sender, subscribers are bcc'd (minus anyone
 * already addressed), and the reply-to depends on whether the sender is on the
 * list.
 */
readonly class IncomingMailHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private SystemFromAddress $systemFromAddress,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(
        ImapMailbox $mailbox,
        MailingList $mailingList,
        IncomingMail $incomingMail,
    ): void {
        $email = new Email();

        // Send from the system address but preserve the sender's display name.
        $email->from(new Address(
            $this->systemFromAddress->address->getAddress(),
            $incomingMail->fromName,
        ));

        // The list address in the To header is decorative; real recipients are
        // bcc'd below.
        $email->to($mailingList->listAddress);

        $email->subject($incomingMail->subject);

        $bcc = $this->bccAddresses(
            mailingList: $mailingList,
            incomingMail: $incomingMail,
        );

        if ($bcc !== []) {
            $email->bcc(...$bcc);
        }

        $email->replyTo($this->replyToAddress(
            mailingList: $mailingList,
            incomingMail: $incomingMail,
        ));

        $html = $this->applyAttachments(email: $email, incomingMail: $incomingMail);

        if ($incomingMail->textPlain !== '') {
            $email->text($incomingMail->textPlain);
        }

        if ($html !== '') {
            $email->html($html);
        }

        $this->send(
            mailbox: $mailbox,
            incomingMail: $incomingMail,
            email: $email,
        );
    }

    private function send(
        ImapMailbox $mailbox,
        IncomingMail $incomingMail,
        Email $email,
    ): void {
        try {
            $this->mailer->send($email);
        } catch (Throwable $error) {
            // Surface the real send failure before touching the mailbox, so a
            // secondary move failure can't mask the underlying cause.
            $this->logger->error('Failed to forward list message', [
                'subject' => $incomingMail->subject,
                'from' => $incomingMail->fromAddress,
                'message' => $error->getMessage(),
            ]);

            $this->moveToDrafts(mailbox: $mailbox, incomingMail: $incomingMail);

            throw $error;
        }

        $mailbox->delete(incomingMail: $incomingMail);
    }

    private function moveToDrafts(
        ImapMailbox $mailbox,
        IncomingMail $incomingMail,
    ): void {
        try {
            $mailbox->moveToDrafts(incomingMail: $incomingMail);
        } catch (Throwable $moveError) {
            $this->logger->error('Failed to move undelivered message to Drafts', [
                'message' => $moveError->getMessage(),
            ]);
        }
    }

    private function replyToAddress(
        MailingList $mailingList,
        IncomingMail $incomingMail,
    ): Address {
        // Internal senders (and unknown senders) get replies routed back to the
        // whole list; external senders get direct replies.
        if (
            $incomingMail->fromAddress === ''
            || $mailingList->subscribers->hasEmailAddress($incomingMail->fromAddress)
        ) {
            return new Address(
                $mailingList->listAddress,
                $mailingList->listName,
            );
        }

        return new Address($incomingMail->fromAddress, $incomingMail->fromName);
    }

    /** @return Address[] */
    private function bccAddresses(
        MailingList $mailingList,
        IncomingMail $incomingMail,
    ): array {
        $excluded = $this->excludedEmails(incomingMail: $incomingMail);

        $addresses = [];

        foreach ($mailingList->subscribers->items as $subscriber) {
            $email = trim($subscriber->emailAddress);

            if ($email === '') {
                continue;
            }

            if (in_array(strtolower($email), $excluded, true)) {
                continue;
            }

            $addresses[] = new Address($email, $subscriber->name);
        }

        return $addresses;
    }

    /** @return string[] */
    private function excludedEmails(IncomingMail $incomingMail): array
    {
        $all = array_merge(
            [$incomingMail->fromAddress],
            $incomingMail->toAddresses,
            $incomingMail->ccAddresses,
        );

        return array_values(array_map(
            static fn (string $email): string => strtolower(trim($email)),
            $all,
        ));
    }

    private function applyAttachments(
        Email $email,
        IncomingMail $incomingMail,
    ): string {
        $html = $incomingMail->textHtml;

        foreach ($incomingMail->attachments->items as $attachment) {
            if ($this->isInlineImage(attachment: $attachment, html: $html)) {
                $email->embed(
                    $attachment->contentBytes,
                    $attachment->filename,
                    $attachment->contentType,
                );

                $html = $this->rewriteInlineReference(
                    attachment: $attachment,
                    html: $html,
                );

                continue;
            }

            $email->attach(
                $attachment->contentBytes,
                $attachment->filename,
                $attachment->contentType,
            );
        }

        return $html;
    }

    private function isInlineImage(
        IncomingAttachment $attachment,
        string $html,
    ): bool {
        if ($attachment->contentId === '') {
            return false;
        }

        return preg_match($this->cidPattern($attachment->contentId), $html) === 1;
    }

    private function rewriteInlineReference(
        IncomingAttachment $attachment,
        string $html,
    ): string {
        // Symfony references an embedded part as `cid:<name>`, so point the
        // original content-id reference at the embedded filename.
        return (string) preg_replace(
            $this->cidPattern($attachment->contentId),
            'src="cid:' . $attachment->filename . '"',
            $html,
        );
    }

    private function cidPattern(string $contentId): string
    {
        return '/src="cid:' . preg_quote($contentId, '/') . '"/';
    }
}
