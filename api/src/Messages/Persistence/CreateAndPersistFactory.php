<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

use App\Messages\FileManager\FileRepository;
use App\Messages\Message\Message;
use App\Persistence\PersistNewRecord;
use App\Persistence\PersistRecord;
use App\Persistence\Result;

readonly class CreateAndPersistFactory
{
    public function __construct(
        private FindBySlug $findBySlug,
        private Transformer $transformer,
        private PersistRecord $persistRecord,
        private FileRepository $fileRepository,
        private PersistNewRecord $persistNewRecord,
    ) {
    }

    public function create(Message $message): Result
    {
        $message = $this->validate($message);

        if (! $message->isValid) {
            return new Result(
                false,
                $message->errorMessages,
            );
        }

        $record = $this->transformer->createRecord($message);

        $this->postValidate($message);

        return $this->persistNewRecord->persist($record);
    }

    public function persist(Message $message): Result
    {
        $message = $this->validate($message);

        if (! $message->isValid) {
            return new Result(
                false,
                $message->errorMessages,
            );
        }

        $record = $this->transformer->createRecord($message);

        $this->postValidate($message);

        return $this->persistRecord->persist($record);
    }

    private function validate(Message $message): Message
    {
        if (
            $message->audioFileName->upload->hasData() &&
            $this->fileRepository->fileExists(
                $message->audioFileName->audioFileName,
            )
        ) {
            $message = $message->withErrorMessage(
                'The name of the audio file you are uploading already exists on the server',
            );
        }

        $existingSlug = $this->findBySlug->find(
            $message->slug,
            $message->id,
        );

        if ($existingSlug !== null) {
            $message = $message->withErrorMessage(
                'Specified slug already exists. Message slug must be unique',
            );
        }

        return $message;
    }

    private function postValidate(Message $message): void
    {
        if (! $message->audioFileName->upload->hasData()) {
            return;
        }

        $this->fileRepository->saveBase64FileToDisk(
            $message->audioFileName->upload->name,
            $message->audioFileName->upload->data,
        );
    }
}
