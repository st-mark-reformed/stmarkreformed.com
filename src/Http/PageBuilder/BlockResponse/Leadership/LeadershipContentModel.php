<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\Leadership;

use function array_map;

class LeadershipContentModel
{
    /** @var LeadershipSectionContentModel[] */
    private array $leadershipSections;

    /**
     * @param LeadershipSectionContentModel[] $leadershipSections
     */
    public function __construct(array $leadershipSections)
    {
        array_map([$this, 'addSection'], $leadershipSections);
    }

    private function addSection(LeadershipSectionContentModel $section): void
    {
        $this->leadershipSections[] = $section;
    }

    /**
     * @return LeadershipSectionContentModel[]
     */
    public function leadershipSections(): array
    {
        return $this->leadershipSections;
    }
}
