<?php

declare(strict_types=1);

namespace App\Uploads;

use App\Result\Result;
use Psr\Clock\ClockInterface;
use Ramsey\Uuid\UuidFactoryInterface;

use function base64_decode;

/**
 * Orchestrates the resumable upload lifecycle (initiate, write chunk, status,
 * complete) over the staging store, assembler, and content validator. The HTTP
 * actions stay thin and delegate here.
 */
readonly class UploadRepository
{
    public function __construct(
        private UploadChunkStore $chunkStore,
        private UploadAssembler $assembler,
        private UploadContentTypeValidator $validator,
        private UuidFactoryInterface $uuidFactory,
        private ClockInterface $clock,
    ) {
    }

    public function initiate(
        string $fileName,
        int $totalSize,
        int $chunkSize,
        int $totalChunks,
        UploadAcceptType $acceptType,
    ): UploadSession {
        $session = new UploadSession(
            uploadId: $this->uuidFactory->uuid4()->toString(),
            fileName: $fileName,
            totalSize: $totalSize,
            chunkSize: $chunkSize,
            totalChunks: $totalChunks,
            acceptType: $acceptType,
            createdAt: $this->clock->now()->getTimestamp(),
        );

        $this->chunkStore->createSession(session: $session);

        return $session;
    }

    public function writeChunk(
        string $uploadId,
        int $index,
        string $base64Data,
    ): Result {
        $decoded = base64_decode($base64Data, true);

        if ($decoded === false) {
            return new Result(success: false, errors: ['upload' => 'The uploaded chunk was not valid.']);
        }

        $this->chunkStore->writeChunk(
            uploadId: $uploadId,
            index: $index,
            bytes: $decoded,
        );

        return new Result();
    }

    public function receivedChunks(string $uploadId): ReceivedChunkIndices
    {
        return $this->chunkStore->receivedChunks(uploadId: $uploadId);
    }

    public function readSession(string $uploadId): UploadSession|null
    {
        return $this->chunkStore->readSession(uploadId: $uploadId);
    }

    public function complete(string $uploadId): Result
    {
        $session = $this->chunkStore->readSession(uploadId: $uploadId);

        if ($session === null) {
            return new Result(success: false, errors: ['upload' => 'The upload session was not found.']);
        }

        $assembleResult = $this->assembler->assemble(session: $session);

        if (! $assembleResult->success) {
            return $assembleResult;
        }

        return $this->validator->validate(
            acceptType: $session->acceptType,
            absoluteFilePath: $this->chunkStore->assembledPath(
                uploadId: $uploadId,
            ),
        );
    }
}
