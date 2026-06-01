<?php

declare(strict_types=1);

namespace App\InternalMessages;

use App\Profiles\Profile;

readonly class SpeakerInternalMessages
{
    public function __construct(
        public Profile $speaker,
        public InternalMessages $messages,
    ) {
    }
}
