<?php

declare(strict_types=1);

namespace App\MailingLists;

readonly class MailingListValidation
{
    /** @return string[] */
    public static function validate(
        NewMailingList|MailingList $mailingList,
    ): array {
        $messages = [];

        if ($mailingList->listName === '') {
            $messages[] = 'List name is required';
        }

        if ($mailingList->listAddress === '') {
            $messages[] = 'List address is required';
        }

        if ($mailingList->imapServer === '') {
            $messages[] = 'IMAP server is required';
        }

        if ($mailingList->imapPort < 1) {
            $messages[] = 'IMAP port must be greater than zero';
        }

        if ($mailingList->username === '') {
            $messages[] = 'Username is required';
        }

        if ($mailingList->password === '') {
            $messages[] = 'Password is required';
        }

        return $messages;
    }
}
