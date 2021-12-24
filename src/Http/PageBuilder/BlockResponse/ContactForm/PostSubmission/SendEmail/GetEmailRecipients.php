<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail;

use App\Email\Entities\EmailRecipient;
use App\Email\Entities\EmailRecipientCollection;
use App\Http\Shared\Exceptions\InvalidEmailAddress;
use craft\config\GeneralConfig;
use craft\elements\GlobalSet;
use craft\errors\InvalidFieldException;
use craft\services\Globals;

use function array_map;
use function assert;
use function is_array;

class GetEmailRecipients
{
    public function __construct(
        private Globals $globals,
        private GeneralConfig $generalConfig,
    ) {
    }

    /**
     * @throws InvalidEmailAddress
     * @throws InvalidFieldException
     */
    public function get(): EmailRecipientCollection
    {
        if ($this->generalConfig->devMode) {
            return new EmailRecipientCollection(
                recipients: [
                    new EmailRecipient(emailAddress: 'tj@buzzingpixel.com'),
                ],
            );
        }

        $generalSet = $this->globals->getSetByHandle(
            'general',
        );

        assert($generalSet instanceof GlobalSet);

        $contactRecipients = $generalSet->getFieldValue(
            'contactFormRecipients',
        );

        assert(is_array($contactRecipients));

        return new EmailRecipientCollection(
            recipients: array_map(
                static function (array $item): EmailRecipient {
                    return new EmailRecipient(
                        emailAddress: (string) $item['emailAddress'],
                    );
                },
                $contactRecipients,
            ),
        );
    }
}
