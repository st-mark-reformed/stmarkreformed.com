<?php

declare(strict_types=1);

namespace App\ManageUsers;

use App\Html\HtmlFormInputConfig;
use App\Html\HtmlFormInputType;
use App\User\UserRole;
use App\User\UserRoles;

use function array_map;

readonly class RoleCheckboxesFactory
{
    /** @return HtmlFormInputConfig[] */
    public function create(UserRoles $selectedRoles): array
    {
        return array_map(
            static fn (UserRole $role): HtmlFormInputConfig => new HtmlFormInputConfig(
                label: $role->name,
                name: 'roles[]',
                value: $role->name,
                type: HtmlFormInputType::checkbox,
                checked: $selectedRoles->has($role),
            ),
            UserRole::cases(),
        );
    }
}
