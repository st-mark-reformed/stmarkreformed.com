<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\Leadership;

use function array_map;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class LeadershipSectionContentModel
{
    /** @var LeadershipPersonContentModel[] */
    private array $people;

    /**
     * @param LeadershipPersonContentModel[] $people
     */
    public function __construct(
        private string $sectionTitle,
        array $people,
    ) {
        array_map([$this, 'addPerson'], $people);
    }

    private function addPerson(LeadershipPersonContentModel $person): void
    {
        $this->people[] = $person;
    }

    public function sectionTitle(): string
    {
        return $this->sectionTitle;
    }

    /**
     * @return LeadershipPersonContentModel[]
     */
    public function people(): array
    {
        return $this->people;
    }
}
