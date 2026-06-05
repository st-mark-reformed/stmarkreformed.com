<?php

declare(strict_types=1);

namespace App\MailingLists\Check;

use App\MailingLists\ConnectionType;
use App\MailingLists\MailingList;
use Webklex\PHPIMAP\ClientManager;

/**
 * Builds and connects a webklex IMAP client for a given mailing list, returning
 * it wrapped in an {@see ImapMailbox}. Construction of the library's
 * ClientManager is contained here, so no DI binding is required.
 */
readonly class ImapClientFactory
{
    public function __construct(
        private IncomingMailFactory $incomingMailFactory,
    ) {
    }

    public function connect(MailingList $mailingList): ImapMailbox
    {
        $client = (new ClientManager())->make([
            'host' => $mailingList->imapServer,
            'port' => $mailingList->imapPort,
            'encryption' => $this->encryption($mailingList->connectionType),
            'validate_cert' => true,
            'username' => $mailingList->username,
            'password' => $mailingList->password,
            'protocol' => 'imap',
        ]);

        $client->connect();

        return new ImapMailbox(
            client: $client,
            incomingMailFactory: $this->incomingMailFactory,
        );
    }

    private function encryption(ConnectionType $connectionType): string
    {
        return match ($connectionType) {
            ConnectionType::Ssl => 'ssl',
            ConnectionType::Tls => 'tls',
            ConnectionType::None => 'notls',
        };
    }
}
