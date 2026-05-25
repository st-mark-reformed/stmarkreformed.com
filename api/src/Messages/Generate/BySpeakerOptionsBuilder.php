<?php

declare(strict_types=1);

namespace App\Messages\Generate;

use App\Profiles\Profile;
use App\Profiles\ProfileLeadershipPosition;
use Redis;

use function json_encode;
use function ksort;

readonly class BySpeakerOptionsBuilder
{
    public function __construct(private Redis $redis)
    {
    }

    /** @param Profile[] $speakersWithMessages */
    public function build(array $speakersWithMessages): void
    {
        $leadership = [];
        $others     = [];

        foreach ($speakersWithMessages as $speaker) {
            if ($speaker->leadershipPosition === ProfileLeadershipPosition::none) {
                $others[$speaker->slug] = $speaker->fullNameWithHonorific;

                continue;
            }

            $leadership[$speaker->slug] = $speaker->fullNameWithHonorific;
        }

        ksort($leadership);

        ksort($others);

        $this->redis->set(
            MessagesRedisKey::bySpeakerOptions(),
            json_encode([
                'leadership' => $leadership,
                'others' => $others,
            ]),
        );
    }
}
