<?php

declare(strict_types=1);

namespace App\MailingLists\Check;

readonly class IncomingAttachment
{
    public function __construct(
        public string $contentId,
        public string $filename,
        public string $contentBytes,
        public string $contentType,
    ) {
    }
}
