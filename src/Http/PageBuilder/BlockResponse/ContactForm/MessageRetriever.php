<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm;

use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\FormValues;
use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail\SendEmailResult;
use Slim\Flash\Messages as FlashMessages;

use function assert;

/**
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedAssignment
 * @psalm-suppress MixedInferredReturnType
 * @psalm-suppress MixedReturnStatement
 * @psalm-suppress PossiblyNullArrayAccess
 */
class MessageRetriever
{
    public function __construct(private FlashMessages $messages)
    {
    }

    public function retrieveFormValuesFromMessage(): ?FormValues
    {
        $message = $this->messages->getMessage('ContactFormMessage');

        $result = $message[0] ?? null;

        if ($result === null) {
            return null;
        }

        assert($result instanceof SendEmailResult);

        return $result->formValues();
    }
}
