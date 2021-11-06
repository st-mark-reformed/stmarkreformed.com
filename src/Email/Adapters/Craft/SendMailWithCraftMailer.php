<?php

declare(strict_types=1);

namespace App\Email\Adapters\Craft;

use App\Email\Entities\Email;
use App\Email\Entities\EmailRecipient;
use App\Email\Entities\EmailResult;
use App\Email\SendMailContract;
use craft\mail\Mailer as CraftMailer;
use craft\mail\Message as CraftMessage;
use craft\models\MailSettings as CraftMailSettings;

use function assert;

class SendMailWithCraftMailer implements SendMailContract
{
    /**
     * @phpstan-ignore-next-line
     */
    public function __construct(
        private CraftMailer $craftMailer,
        /** @phpstan-ignore-next-line */
        private CraftMailSettings $craftMailSettings,
    ) {
    }

    public function send(Email $email): EmailResult
    {
        $craftMessage = new CraftMessage();

        $craftMessage->setSubject($email->subject()->toString());

        $to = [];

        $email->recipients()->map(
            static function (EmailRecipient $r) use (&$to): void {
                $address = $r->emailAddress()->toString();

                $name = $r->name()->toString();

                /** @psalm-suppress MixedArrayAssignment */
                $to[$address] = $name;
            },
        );

        /**
         * @phpstan-ignore-next-line
         * @psalm-suppress MixedArgument
         */
        $craftMessage->setTo($to);

        $from = $email->from();

        assert($from instanceof EmailRecipient);

        $fromAddress = (string) $this->craftMailSettings->fromEmail;

        $fromName = (string) $this->craftMailSettings->fromName;

        /** @phpstan-ignore-next-line */
        $craftMessage->setFrom([$fromAddress => $fromName]);

        $replyToAddress = $from->emailAddress()->toString();

        $replyToName = $from->name()->toString();

        /** @phpstan-ignore-next-line */
        $craftMessage->setReplyTo([$replyToAddress => $replyToName]);

        $craftMessage->setTextBody($email->plaintext()->toString());

        $craftMessage->setHtmlBody($email->html()->toString());

        return new EmailResult(
            sentSuccessfully: $this->craftMailer->send($craftMessage),
        );
    }
}
