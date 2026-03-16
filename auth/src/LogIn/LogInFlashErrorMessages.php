<?php

declare(strict_types=1);

namespace App\LogIn;

use Slim\Flash\Messages as FlashMessages;

use function array_map;
use function is_array;
use function json_decode;
use function json_encode;

readonly class LogInFlashErrorMessages
{
    public function __construct(private FlashMessages $flashMessages)
    {
    }

    public function sendToNextRequest(LogInFlashErrorMessage $message): void
    {
        $this->flashMessages->addMessage(
            'log_in_errors',
            json_encode($message->asArray()),
        );
    }

    public function retrieveMessages(): LogInFlashErrorMessageCollection
    {
        $messages = $this->flashMessages->getMessage(
            'log_in_errors',
        );

        $messages = is_array($messages) ? $messages : [];

        /** @phpstan-ignore-next-line */
        return new LogInFlashErrorMessageCollection(array_map(
            /** @phpstan-ignore-next-line */
            static function (string $json): LogInFlashErrorMessage {
                $data = json_decode($json, true);

                return new LogInFlashErrorMessage(
                    /** @phpstan-ignore-next-line */
                    $data['title'],
                    /** @phpstan-ignore-next-line */
                    $data['body'],
                );
            },
            $messages,
        ));
    }
}
