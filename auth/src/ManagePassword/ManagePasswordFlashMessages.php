<?php

declare(strict_types=1);

namespace App\ManagePassword;

use Slim\Flash\Messages as FlashMessages;

use function array_map;
use function is_array;
use function json_decode;
use function json_encode;

readonly class ManagePasswordFlashMessages
{
    public function __construct(private FlashMessages $flashMessages)
    {
    }

    public function sendToNextRequest(ManagePasswordFlashMessage $message): void
    {
        $this->flashMessages->addMessage(
            'manage_password_messages',
            json_encode($message->asArray()),
        );
    }

    public function retrieveMessages(): ManagePasswordFlashMessageCollection
    {
        $messages = $this->flashMessages->getMessage(
            'manage_password_messages',
        );

        $messages = is_array($messages) ? $messages : [];

        /** @phpstan-ignore-next-line */
        return new ManagePasswordFlashMessageCollection(array_map(
            /** @phpstan-ignore-next-line */
            static function (string $json): ManagePasswordFlashMessage {
                $data = json_decode($json, true);

                return new ManagePasswordFlashMessage(
                    /** @phpstan-ignore-next-line */
                    $data['title'],
                    /** @phpstan-ignore-next-line */
                    MessageType::from($data['type']),
                    /** @phpstan-ignore-next-line */
                    $data['body'],
                );
            },
            $messages,
        ));
    }
}
