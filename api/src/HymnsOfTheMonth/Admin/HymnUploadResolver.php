<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Admin;

use App\HymnsOfTheMonth\HymnPracticeTrack;
use App\HymnsOfTheMonth\HymnPracticeTracks;
use App\HymnsOfTheMonth\Persistence\Persist\HymnFileStorage;
use Cocur\Slugify\Slugify;
use RxAnte\AppBootstrap\Request\ServerRequest;

use function is_array;
use function is_string;
use function str_starts_with;

/**
 * Turns the admin create/edit request into resolved, on-disk file paths.
 *
 * Each upload field is either a new file (a base64 data URI, which gets written
 * to disk) or an already-stored relative path that is kept as-is. New files are
 * detected by the "data:" prefix; stored paths never start with it. Files are
 * written here, in the admin layer, so the domain entity only ever holds real
 * relative paths — the same shape the importer and Redis generator rely on. A
 * failed DB write afterward could orphan a freshly written file under the hymn's
 * own {slug} folder; it is harmless and overwritten on the next save.
 */
readonly class HymnUploadResolver
{
    public function __construct(private HymnFileStorage $storage)
    {
    }

    public function resolveMusicSheetPath(
        string $slug,
        ServerRequest $request,
    ): string {
        $value = $request->parsedBody->getString(name: 'musicSheet');

        if ($value === '') {
            return '';
        }

        if (! $this->isUpload(value: $value)) {
            return $value;
        }

        return $this->storage->saveSheet(dataUri: $value, slug: $slug);
    }

    public function resolvePracticeTracks(
        string $slug,
        ServerRequest $request,
    ): HymnPracticeTracks {
        $raw = $request->parsedBody->attributes['practiceTracks'] ?? null;

        if (! is_array($raw)) {
            return new HymnPracticeTracks();
        }

        $tracks = [];
        $index  = 0;

        foreach ($raw as $rawTrack) {
            if (! is_array($rawTrack)) {
                continue;
            }

            $track = $this->resolveTrack(
                slug: $slug,
                rawTrack: $rawTrack,
                index: $index,
            );

            $index++;

            if ($track === null) {
                continue;
            }

            $tracks[] = $track;
        }

        return new HymnPracticeTracks(tracks: $tracks);
    }

    /** @param array<array-key, mixed> $rawTrack */
    private function resolveTrack(
        string $slug,
        array $rawTrack,
        int $index,
    ): HymnPracticeTrack|null {
        $title = $this->stringValue(value: $rawTrack['title'] ?? null);
        $file  = $this->stringValue(value: $rawTrack['file'] ?? null);

        $path = $this->isUpload(value: $file)
            ? $this->storage->saveTrack(
                dataUri: $file,
                slug: $slug,
                fileNameBase: $this->trackFileNameBase(title: $title, index: $index),
            )
            : $file;

        if ($title === '' && $path === '') {
            return null;
        }

        return new HymnPracticeTrack(title: $title, path: $path);
    }

    private function trackFileNameBase(string $title, int $index): string
    {
        $base = new Slugify()->slugify($title);

        return $base === '' ? 'track-' . $index : $base;
    }

    private function isUpload(string $value): bool
    {
        return str_starts_with($value, 'data:');
    }

    private function stringValue(mixed $value): string
    {
        return is_string($value) ? $value : '';
    }
}
