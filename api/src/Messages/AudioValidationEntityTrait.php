<?php

declare(strict_types=1);

namespace App\Messages;

use function base64_decode;
use function ord;
use function preg_match;
use function str_starts_with;
use function strlen;
use function strpos;
use function substr;

// phpcs:disabled SlevomatCodingStandard.Classes.SuperfluousTraitNaming.SuperfluousSuffix

trait AudioValidationEntityTrait
{
    private function isCanonicalBase64(string $value): bool
    {
        if ($value === '') {
            return false;
        }

        if (preg_match('/^[A-Za-z0-9+\/]+={0,2}$/', $value) !== 1) {
            return false;
        }

        if (strlen($value) % 4 !== 0) {
            return false;
        }

        return base64_decode($value, true) !== false;
    }

    public function audioPathIsFileUpload(): bool
    {
        if ($this->audioPath === '') {
            return false;
        }

        $audioPath = $this->audioPath;

        if (str_starts_with($audioPath, 'data:')) {
            $commaPosition = strpos($audioPath, ',');

            if ($commaPosition === false) {
                return false;
            }

            $audioPath = substr($audioPath, $commaPosition + 1);
        }

        return $this->isCanonicalBase64($audioPath);
    }

    public function audioPathIsValidFileUpload(): bool
    {
        if ($this->audioPath === '') {
            return false;
        }

        $audioPath = $this->audioPath;

        if (str_starts_with($audioPath, 'data:')) {
            $commaPosition = strpos($audioPath, ',');

            if ($commaPosition === false) {
                return false;
            }

            $audioPath = substr($audioPath, $commaPosition + 1);
        }

        $decoded = base64_decode($audioPath, true);

        if ($decoded === false || $decoded === '') {
            return false;
        }

        if (str_starts_with($decoded, 'ID3')) {
            return true;
        }

        if (strlen($decoded) < 2) {
            return false;
        }

        return ord($decoded[0]) === 0xFF
            && (ord($decoded[1]) & 0xE0) === 0xE0;
    }

    public function createAudioFileNameForPersistence(): string
    {
        if (! $this->audioPathIsFileUpload()) {
            return $this->audioPath;
        }

        return $this->getAudioFileName();
    }

    public function getAudioFileName(): string
    {
        return $this->slug . '.mp3';
    }
}
