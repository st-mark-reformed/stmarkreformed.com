<?php

declare(strict_types=1);

namespace App\User\CLI;

use App\Cli\CliQuestion;
use App\User\UserRole;

use function array_filter;
use function array_map;
use function count;
use function explode;
use function implode;
use function trim;

readonly class CliCollectRole
{
    public function __construct(private CliQuestion $question)
    {
    }

    /**
     * @param string[] $role
     *
     * @return string[]
     */
    public function collect(array $role = []): array
    {
        if (count($role) > 0) {
            return $role;
        }

        return array_map(
            static fn (string $r) => trim($r),
            array_filter(
                explode(',', $this->question->ask(
                    'Role(s) ' . implode(
                        ', ',
                        array_map(
                            static fn (UserRole $r) => $r->name,
                            UserRole::cases(),
                        ),
                    ) . ': ',
                )),
                static fn (string $r) => $r !== '',
            ),
        );
    }
}
