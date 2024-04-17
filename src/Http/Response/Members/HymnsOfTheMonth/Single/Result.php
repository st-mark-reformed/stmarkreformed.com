<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth\Single;

use function array_map;

class Result
{
    /** @var Track[] */
    private array $tracks;

    /**
     * @param Track[] $tracks
     */
    public function __construct(
        private string $month,
        private string $year,
        private string $title,
        private ?string $musicSheetDownloadUrl,
        array $tracks,
    ) {
        $this->tracks = [];

        array_map(
            function (Track $track): void {
                $this->tracks[] = $track;
            },
            $tracks,
        );
    }

    public function month(): string
    {
        return $this->month;
    }

    public function year(): string
    {
        return $this->year;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function musicSheetDownloadUrl(): ?string
    {
        return $this->musicSheetDownloadUrl;
    }

    /**
     * @return Track[]
     */
    public function tracks(): array
    {
        return $this->tracks;
    }

    /**
     * @return mixed[]
     */
    public function mapTracks(callable $callable): array
    {
        return array_map($callable, $this->tracks());
    }
}
