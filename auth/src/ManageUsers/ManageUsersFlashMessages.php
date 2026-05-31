<?php

declare(strict_types=1);

namespace App\ManageUsers;

use Slim\Flash\Messages as FlashMessages;

use function array_map;
use function is_array;
use function json_decode;
use function json_encode;

readonly class ManageUsersFlashMessages
{
    public function __construct(private FlashMessages $flashMessages)
    {
    }

    public function sendToNextRequest(ManageUsersFlashMessage $message): void
    {
        $this->flashMessages->addMessage(
            'manage_users_messages',
            json_encode($message->asArray()),
        );
    }

    public function sendError(string $title): void
    {
        $this->sendToNextRequest(new ManageUsersFlashMessage(
            title: $title,
            type: MessageType::error,
        ));
    }

    public function sendSuccess(string $title): void
    {
        $this->sendToNextRequest(new ManageUsersFlashMessage(
            title: $title,
            type: MessageType::success,
        ));
    }

    public function retrieveMessages(): ManageUsersFlashMessageCollection
    {
        $messages = $this->flashMessages->getMessage(
            'manage_users_messages',
        );

        $messages = is_array($messages) ? $messages : [];

        /** @phpstan-ignore-next-line */
        return new ManageUsersFlashMessageCollection(array_map(
            /** @phpstan-ignore-next-line */
            static function (string $json): ManageUsersFlashMessage {
                $data = json_decode($json, true);

                return new ManageUsersFlashMessage(
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
