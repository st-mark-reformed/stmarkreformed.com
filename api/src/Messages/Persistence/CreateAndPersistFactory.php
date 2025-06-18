<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

use App\Messages\FileManager\FileRepository;
use App\Messages\Message\Message;
use App\Persistence\PersistNewRecord;
use App\Persistence\Result;

readonly class CreateAndPersistFactory
{
    public function __construct(
        private Transformer $transformer,
        private FileRepository $fileRepository,
        private PersistNewRecord $persistNewRecord,
    ) {
    }

    public function create(Message $message): Result
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

        if (! $message->isValid) {
            return new Result(
                false,
                $message->errorMessages,
            );
        }

        $messageRecord = $this->transformer->createRecord(
            $message,
        );

        if ($message->audioFileName->upload->hasData()) {
            $this->fileRepository->saveBase64FileToDisk(
                $message->audioFileName->upload->name,
                $message->audioFileName->upload->data,
            );
        }

        return $this->persistNewRecord->persist($messageRecord);
    }
}
