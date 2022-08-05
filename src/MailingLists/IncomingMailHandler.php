<?php

declare(strict_types=1);

namespace App\MailingLists;

use craft\mail\Mailer as CraftMailer;
use craft\mail\Message as CraftMessage;
use Exception;
use PhpImap\IncomingMail;
use PhpImap\IncomingMailAttachment;
use PhpImap\Mailbox;

use function array_keys;
use function array_map;
use function count;
use function in_array;
use function preg_match;
use function preg_replace;

class IncomingMailHandler
{
    public function __construct(private CraftMailer $craftMailer)
    {
    }

    /**
     * @throws Exception
     */
    public function handle(
        Mailbox $mailbox,
        MailingList $mailingList,
        IncomingMail $incomingMail,
    ): void {
        $craftMessage = new CraftMessage();

        // Decorative header, not actually sent. Recipients of list will be
        // in the bcc field
        $craftMessage->setHeader(
            'to',
            $mailingList->listAddress(),
        );

        $craftMessage->setSubject($incomingMail->subject);

        $fromAddress = (string) $incomingMail->fromAddress;

        $fromName = (string) $incomingMail->fromName;

        /** @phpstan-ignore-next-line */
        $craftMessage->setFrom([$fromAddress => $fromName]);

        $to = [];

        $mailingList->subscribers()->map(
            static function (Subscriber $subscriber) use (
                &$to,
                $incomingMail,
            ): void {
                $address = $subscriber->emailAddress();

                $name = $subscriber->name();

                if ($address === $incomingMail->fromAddress) {
                    return;
                }

                if (
                    in_array(
                        $address,
                        array_keys($incomingMail->to),
                        true,
                    )
                ) {
                    return;
                }

                if (
                    in_array(
                        $address,
                        array_keys($incomingMail->cc),
                        true,
                    )
                ) {
                    return;
                }

                $to[$address] = $name;
            }
        );

        /** @phpstan-ignore-next-line */
        $craftMessage->setBcc($to);

        $craftMessage->setReplyTo($mailingList->listAddress());

        if (count($incomingMail->getAttachments()) > 0) {
            array_map(
                static function (
                    IncomingMailAttachment $attachment,
                ) use (
                    $craftMessage,
                    $incomingMail
                ): void {
                    preg_match(
                        '/src="cid:' . $attachment->id . '"/',
                        $incomingMail->textHtml,
                        $matches,
                    );

                    if (count($matches) < 1) {
                        $craftMessage->attach(
                            $attachment->filePath,
                            ['fileName' => $attachment->name],
                        );

                        return;
                    }

                    $cid = $craftMessage->embed(
                        $attachment->filePath,
                        ['fileName' => $attachment->name],
                    );

                    $incomingMail->textHtml = preg_replace(
                        '/src="cid:' . $attachment->id . '"/',
                        'src="' . $cid . '"',
                        $incomingMail->textHtml,
                    );
                },
                $incomingMail->getAttachments(),
            );
        }

        $craftMessage->setTextBody($incomingMail->textPlain);

        $craftMessage->setHtmlBody($incomingMail->textHtml);

        if (! $this->craftMailer->send($craftMessage)) {
            throw new Exception('Unable to send email');
        }

        $mailbox->deleteMail($incomingMail->id);
    }
}
