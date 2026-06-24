<?php

declare(strict_types=1);

namespace App\Messages\Admin\NewMessage;

use App\EmptyUuid;
use App\Messages\NewMessage;
use App\Uploads\UploadHandleSize;
use DateTimeImmutable;
use DateTimeZone;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use RxAnte\AppBootstrap\Request\ServerRequest;
use Throwable;

readonly class NewMessageFactory
{
    public function __construct(private UploadHandleSize $uploadHandleSize)
    {
    }

    public function createFromRequest(ServerRequest $request): NewMessage
    {
        $audioPath = $request->parsedBody->getString(name: 'audioPath');

        return new NewMessage(
            isEnabled: $request->parsedBody->getBoolean(name: 'isEnabled'),
            date: $this->getDate(
                date: $request->parsedBody->getString(name: 'date'),
            ),
            title: $request->parsedBody->getString(name: 'title'),
            audioPath: $audioPath,
            audioFileSize: $this->uploadHandleSize->forValue($audioPath),
            speakerId: $this->getId(
                id: $request->parsedBody->getString(name: 'speakerId'),
            ),
            passage: $request->parsedBody->getString(name: 'passage'),
            seriesId: $this->getId(
                id: $request->parsedBody->getString(name: 'seriesId'),
            ),
        );
    }

    private function getDate(string $date): DateTimeImmutable
    {
        $dateObj = DateTimeImmutable::createFromFormat(
            'Y-m-d\TH:i:s',
            $date,
            new DateTimeZone('US/Central'),
        );

        if ($dateObj === false) {
            $dateObj = DateTimeImmutable::createFromFormat(
                'Y-m-d\TH:i',
                $date,
                new DateTimeZone('US/Central'),
            );
        }

        if ($dateObj === false) {
            $dateObj = new DateTimeImmutable()->setTimezone(
                new DateTimeZone('US/Central'),
            );
        }

        return $dateObj;
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
