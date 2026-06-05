<?php

declare(strict_types=1);

namespace App\MailingLists\Admin\EditMailingList\GetEditMailingList;

use App\MailingLists\MailingListResult;
use App\Responder;
use App\RespondWithJson;
use App\RespondWithNotFound;
use Psr\Http\Message\ResponseFactoryInterface;

readonly class EditMailingListResponderFactory
{
    public function __construct(private ResponseFactoryInterface $factory)
    {
    }

    public function create(MailingListResult $result): Responder
    {
        if (! $result->hasMailingList) {
            return new RespondWithNotFound(
                factory: $this->factory,
                message: 'Mailing list not found',
            );
        }

        // The entity serializes without its IMAP password, so the secret is
        // never sent to the browser.
        return new RespondWithJson(
            entity: $result->mailingList,
            factory: $this->factory,
        );
    }
}
