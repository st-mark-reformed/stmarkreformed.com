<?php

declare(strict_types=1);

namespace App\InternalMessages\Generate;

use function preg_match;

readonly class ExistingInternalMediaRedisKeys
{
    /** @var string[] */
    public array $pageKeys;

    /** @var string[] */
    public array $slugKeys;

    /** @var array<string, string[]> indexed by speaker slug */
    public array $bySpeakerKeysBySlug;

    /** @var array<string, string[]> indexed by series slug */
    public array $bySeriesKeysBySlug;

    /** @param string[] $allKeys */
    public function __construct(array $allKeys)
    {
        $pageKeys            = [];
        $slugKeys            = [];
        $bySpeakerKeysBySlug = [];
        $bySeriesKeysBySlug  = [];

        foreach ($allKeys as $key) {
            if (InternalMediaRedisKey::isPageKey($key)) {
                $pageKeys[] = $key;

                continue;
            }

            if (InternalMediaRedisKey::isSlugKey($key)) {
                $slugKeys[] = $key;

                continue;
            }

            if (
                preg_match(
                    '/^api-members:internal_media:by:([^:]+):/',
                    $key,
                    $m,
                ) === 1
            ) {
                $bySpeakerKeysBySlug[$m[1]][] = $key;

                continue;
            }

            if (
                preg_match(
                    '/^api-members:internal_media:series:([^:]+):/',
                    $key,
                    $m,
                ) !== 1
            ) {
                continue;
            }

            $bySeriesKeysBySlug[$m[1]][] = $key;
        }

        $this->pageKeys            = $pageKeys;
        $this->slugKeys            = $slugKeys;
        $this->bySpeakerKeysBySlug = $bySpeakerKeysBySlug;
        $this->bySeriesKeysBySlug  = $bySeriesKeysBySlug;
    }

    /** @return string[] */
    public function bySpeaker(string $speakerSlug): array
    {
        return $this->bySpeakerKeysBySlug[$speakerSlug] ?? [];
    }

    /** @return string[] */
    public function bySeries(string $seriesSlug): array
    {
        return $this->bySeriesKeysBySlug[$seriesSlug] ?? [];
    }
}
