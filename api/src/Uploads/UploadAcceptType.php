<?php

declare(strict_types=1);

namespace App\Uploads;

enum UploadAcceptType: string
{
    case Mp3 = 'mp3';
    case Pdf = 'pdf';
    case Any = 'any';

    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::Any;
    }
}
