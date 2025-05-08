<?php

declare(strict_types=1);

namespace App\Http\Response\LogIn;

use craft\elements\User as UserElement;
use craft\helpers\User as UserHelper;

/**
 * @codeCoverageIgnore
 */
class CraftUserHelperFacade
{
    /**
     * @phpstan-ignore-next-line
     */
    public function getLoginFailureMessage(
        ?string $authError = null,
        ?UserElement $user = null,
    ): string {
        return UserHelper::getLoginFailureMessage(
            $authError,
            $user
        );
    }
}
