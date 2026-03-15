<?php

declare(strict_types=1);

namespace App\Cookies;

enum CookieName
{
    case logged_in_session;
    case stored_username;
    case stored_email;
    case iss;
    case external_oauth_redirect_url;
    case external_oauth_state;
    case external_oauth_pkce_code;
    case external_oauth_iss;

    public static function fromString(string $name): CookieName
    {
        /** @phpstan-ignore-next-line */
        return self::{$name};
    }
}
