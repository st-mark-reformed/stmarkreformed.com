<?php

declare(strict_types=1);

namespace App\Messages;

use App\Profiles\Profile;

readonly class SpeakerMessages
{
    public function __construct(
        public Profile $speaker,
        public Messages $messages,
    ) {
    }
}
