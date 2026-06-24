<?php

declare(strict_types=1);

namespace App\Uploads;

use App\Result\Result;

use function fclose;
use function fopen;
use function fread;
use function ord;
use function str_starts_with;
use function strlen;

/**
 * Validates an assembled upload against the accept type requested at initiate,
 * by reading only the file's header bytes (never the whole file).
 */
readonly class UploadContentTypeValidator
{
    private const int HEADER_BYTES = 16;

    public function validate(
        UploadAcceptType $acceptType,
        string $absoluteFilePath,
    ): Result {
        if ($acceptType === UploadAcceptType::Any) {
            return new Result();
        }

        $header = $this->readHeader($absoluteFilePath);

        if ($header === null) {
            return new Result(success: false, errors: ['upload' => 'Unable to read the uploaded file for validation.']);
        }

        if ($acceptType === UploadAcceptType::Pdf) {
            if (str_starts_with($header, '%PDF-')) {
                return new Result();
            }

            return new Result(success: false, errors: ['upload' => 'The uploaded file is not a valid PDF.']);
        }

        if ($this->isMp3($header)) {
            return new Result();
        }

        return new Result(success: false, errors: ['upload' => 'The uploaded file is not a valid MP3.']);
    }

    private function readHeader(string $path): string|null
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            return null;
        }

        $header = fread($handle, self::HEADER_BYTES);

        fclose($handle);

        if ($header === false) {
            return null;
        }

        return $header;
    }

    private function isMp3(string $header): bool
    {
        if (str_starts_with($header, 'ID3')) {
            return true;
        }

        if (strlen($header) < 2) {
            return false;
        }

        return ord($header[0]) === 0xFF
            && (ord($header[1]) & 0xE0) === 0xE0;
    }
}
