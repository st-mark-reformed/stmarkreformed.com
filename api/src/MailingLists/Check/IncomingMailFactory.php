<?php

declare(strict_types=1);

namespace App\MailingLists\Check;

use Webklex\PHPIMAP\Address;
use Webklex\PHPIMAP\Attachment;
use Webklex\PHPIMAP\Attribute;
use Webklex\PHPIMAP\Message;

use function is_string;

/**
 * Translates a webklex {@see Message} into our own {@see IncomingMail} value
 * object. All knowledge of the IMAP library's attribute shapes lives here.
 */
class IncomingMailFactory
{
    public function fromMessage(Message $message): IncomingMail
    {
        $from = $message->getFrom()->first();

        return new IncomingMail(
            fromAddress: $from instanceof Address ? $from->mail : '',
            fromName: $from instanceof Address ? $from->personal : '',
            subject: $message->getSubject()->toString(),
            toAddresses: $this->emailsFrom($message->getTo()),
            ccAddresses: $this->emailsFrom($message->getCc()),
            textPlain: $message->getTextBody(),
            textHtml: $message->getHTMLBody(),
            attachments: $this->attachmentsFrom($message),
            message: $message,
        );
    }

    /** @return string[] */
    private function emailsFrom(Attribute $addresses): array
    {
        $emails = [];

        foreach ($addresses->all() as $address) {
            if (! $address instanceof Address) {
                continue;
            }

            $emails[] = $address->mail;
        }

        return $emails;
    }

    private function attachmentsFrom(Message $message): IncomingAttachments
    {
        $attachments = [];

        foreach ($message->getAttachments()->all() as $attachment) {
            if (! $attachment instanceof Attachment) {
                continue;
            }

            $attachments[] = new IncomingAttachment(
                contentId: is_string($attachment->getId()) ? $attachment->getId() : '',
                filename: $attachment->getName() ?? '',
                contentBytes: $attachment->getContent() ?? '',
                contentType: $attachment->getMimeType() ?? 'application/octet-stream',
            );
        }

        return new IncomingAttachments(items: $attachments);
    }
}
