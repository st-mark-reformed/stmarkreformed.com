<?php

declare(strict_types=1);

namespace App\Messages\Message;

use Assert\Assertion;
use RuntimeException;
use Throwable;

use function is_array;
use function json_decode;

readonly class AudioFileName
{
    public bool $isValid;

    public string $errorMessage;

    public string $audioFileName;

    public AudioFileUpload $upload;

    public function __construct(string $audioFileName)
    {
        $errorMessage = '';

        try {
            $uploadData = json_decode($audioFileName, true);

            if (! is_array($uploadData)) {
                throw new RuntimeException('No upload data');
            }

            $upload = new AudioFileUpload(
                $uploadData['name'],
                $uploadData['data'],
            );

            $audioFileName = $upload->hasData() ? $upload->name : $audioFileName;

            $this->upload = $upload;
        } catch (Throwable $e) {
            $this->upload = new AudioFileUpload();
        }

        try {
            Assertion::notEmpty(
                $audioFileName,
                'An audio file is required',
            );
        } catch (Throwable $e) {
            $errorMessage = $e->getMessage();
        }

        $this->audioFileName = $audioFileName;

        $this->isValid = $errorMessage === '';

        $this->errorMessage = $errorMessage;
    }
}
