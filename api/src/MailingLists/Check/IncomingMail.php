<?php

declare(strict_types=1);

namespace App\MailingLists\Check;

use Webklex\PHPIMAP\Message;

/**
 * A read-friendly value view over a single incoming IMAP message. The
 * forwarding logic reads only the extracted fields; the underlying webklex
 * {@see Message} is carried solely so {@see ImapMailbox} can move or delete the
 * source message, keeping webklex out of the rest of the domain.
 */
readonly class IncomingMail
{
    /**
     * @param string[] $toAddresses
     * @param string[] $ccAddresses
     */
    public function __construct(
        public string $fromAddress,
        public string $fromName,
        public string $subject,
        public array $toAddresses,
        public array $ccAddresses,
        public string $textPlain,
        public string $textHtml,
        public IncomingAttachments $attachments,
        public Message $message,
    ) {
    }
}
