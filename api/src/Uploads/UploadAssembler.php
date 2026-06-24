<?php

declare(strict_types=1);

namespace App\Uploads;

use App\Result\Result;

use function fclose;
use function feof;
use function filesize;
use function fopen;
use function fread;
use function fwrite;

/**
 * Concatenates the staged chunk parts, in order, into a single assembled file.
 * Streams part-by-part so the whole upload is never held in memory.
 */
readonly class UploadAssembler
{
    private const int READ_BUFFER_BYTES = 1048576;

    public function __construct(private UploadChunkStore $chunkStore)
    {
    }

    public function assemble(UploadSession $session): Result
    {
        $received = $this->chunkStore->receivedChunks($session->uploadId);

        if (! $received->isComplete($session->totalChunks)) {
            return new Result(success: false, errors: ['upload' => 'Upload is incomplete; not all chunks were received.']);
        }

        $assembledPath = $this->chunkStore->assembledPath($session->uploadId);

        $out = fopen($assembledPath, 'wb');

        if ($out === false) {
            return new Result(success: false, errors: ['upload' => 'Unable to assemble the uploaded file.']);
        }

        for ($index = 0; $index < $session->totalChunks; $index++) {
            $in = fopen($this->chunkStore->chunkPath($session->uploadId, $index), 'rb');

            if ($in === false) {
                fclose($out);

                return new Result(success: false, errors: ['upload' => 'Unable to read an uploaded chunk.']);
            }

            while (! feof($in)) {
                $buffer = fread($in, self::READ_BUFFER_BYTES);

                if ($buffer === false) {
                    fclose($in);
                    fclose($out);

                    return new Result(success: false, errors: ['upload' => 'Unable to read an uploaded chunk.']);
                }

                fwrite($out, $buffer);
            }

            fclose($in);
        }

        fclose($out);

        $assembledSize = filesize($assembledPath);

        if ($assembledSize === false || $assembledSize !== $session->totalSize) {
            return new Result(success: false, errors: ['upload' => 'The assembled file size did not match the expected size.']);
        }

        return new Result();
    }
}
