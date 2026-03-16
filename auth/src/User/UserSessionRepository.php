<?php

declare(strict_types=1);

namespace App\User;

use App\Cookies\CookieName;
use App\Cookies\Cookies;
use Psr\Cache\CacheItemPoolInterface;

use function implode;

readonly class UserSessionRepository
{
    public function __construct(
        private Cookies $cookies,
        private CacheItemPoolInterface $cachePool,
    ) {
    }

    private function getSessionKey(string $sessionId): string
    {
        return implode('+', [
            'user_sessions',
            $sessionId,
        ]);
    }

    public function findSessionFromCookies(): UserSession|null
    {
        $cookie = $this->cookies->find(CookieName::logged_in_session);

        if ($cookie === null) {
            return null;
        }

        return $this->findSessionById($cookie->value);
    }

    public function findSessionById(string $id): UserSession|null
    {
        $cachedSession = $this->cachePool->getItem(
            $this->getSessionKey($id),
        )->get();

        if (! $cachedSession instanceof UserSession) {
            return null;
        }

        return $cachedSession;
    }
}
