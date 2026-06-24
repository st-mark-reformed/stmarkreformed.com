<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Admin;

use App\HymnsOfTheMonth\HymnPracticeTrack;
use App\HymnsOfTheMonth\HymnPracticeTracks;
use App\HymnsOfTheMonth\Persistence\Persist\HymnFileStorage;
use App\Uploads\UploadHandle;
use Cocur\Slugify\Slugify;
use RxAnte\AppBootstrap\Request\ServerRequest;

use function is_array;
use function is_string;

/**
 * Turns the admin create/edit request into resolved, on-disk file paths.
 *
 * Each upload field is either a new file (an "upload:{id}" handle for a resumably
 * uploaded file, which gets claimed onto disk) or an already-stored relative path
 * that is kept as-is. New files are detected by the "upload:" prefix; stored paths
 * never start with it. Files are claimed here, in the admin layer, so the domain
 * entity only ever holds real relative paths — the same shape the importer and
 * Redis generator rely on. A failed DB write afterward could orphan a freshly
 * written file under the hymn's own {slug} folder; it is harmless and overwritten
 * on the next save.
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

        if (! UploadHandle::isHandle($value)) {
            return $value;
        }

        return $this->storage->saveSheet(
            uploadId: UploadHandle::fromValue($value)->uploadId,
            slug: $slug,
        );
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

        $path = UploadHandle::isHandle($file)
            ? $this->storage->saveTrack(
                uploadId: UploadHandle::fromValue($file)->uploadId,
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

    private function stringValue(mixed $value): string
    {
        return is_string($value) ? $value : '';
    }
}
