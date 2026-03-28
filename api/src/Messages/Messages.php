<?php

declare(strict_types=1);

namespace App\Messages;

use function array_map;
use function array_values;

readonly class Messages
{
    /** @var Message[] */
    public array $items;

    /** @param Message[] $items */
    public function __construct(array $items)
    {
        $this->items = array_values(array_map(
            static fn (Message $m) => $m,
            $items,
        ));
    }

    /**
     * @return array<array-key, array{
     *     isValid: bool,
     *      date: string,
     *      title: string,
     *      slug: string,
     *      audioPath: string,
     *      speaker: array{
     *          id: string,
     *          titleOrHonorific: string,
     *          firstName: string,
     *          lastName: string,
     *          fullName: string,
     *          fullNameWithHonorific: string,
     *          email: string,
     *          leadershipPosition: string,
     *          leadershipPositionHumanReadable: string,
     *          bio: string,
     *          hasMessages: bool,
     *      },
     *      passage: string,
     *      series: array{
     *          id: string,
     *          title: string,
     *          slug: string,
     *      },
     *      description: string,
     * }>
     */
    public function asArray(): array
    {
        return array_map(
            static fn (Message $i) => $i->asArray(),
            $this->items,
        );
    }
}
