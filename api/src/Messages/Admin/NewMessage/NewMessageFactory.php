<?php

declare(strict_types=1);

namespace App\Messages\Admin\NewMessage;

use App\EmptyUuid;
use App\Messages\NewMessage;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use RxAnte\AppBootstrap\Request\ServerRequest;
use Throwable;

readonly class NewMessageFactory
{
    public function createFromRequest(ServerRequest $request): NewMessage
    {
        // TODO: Deal with audio uploads
        return new NewMessage(
            isEnabled: $request->parsedBody->getBoolean(name: 'isEnabled'),
            date: $this->getDate(request: $request),
            title: $request->parsedBody->getString(name: 'title'),
            speakerId: $this->getId(
                id: $request->parsedBody->getString(name: 'speakerId'),
            ),
            passage: $request->parsedBody->getString(name: 'passage'),
            seriesId: $this->getId(
                id: $request->parsedBody->getString(name: 'seriesId'),
            ),
            audioPath: $request->parsedBody->getString(name: 'audioPath'),
        );
    }

    private function getDate(ServerRequest $request): DateTimeImmutable
    {
        try {
            /** @phpstan-ignore-next-line */
            return DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s',
                $request->parsedBody->getString(name: 'date'),
            );
        } catch (Throwable) {
            return new DateTimeImmutable();
        }
    }

    private function getId(string $id): UuidInterface
    {
        try {
            return Uuid::fromString($id);
        } catch (Throwable) {
            return new EmptyUuid();
        }
    }
}
