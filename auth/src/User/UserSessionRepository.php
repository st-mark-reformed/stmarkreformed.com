<?php

declare(strict_types=1);

namespace App\User;

use App\Cookies\Cookie;
use App\Cookies\CookieName;
use App\Cookies\Cookies;
use DateInterval;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Clock\ClockInterface;
use Ramsey\Uuid\UuidFactoryInterface;

use function implode;

readonly class UserSessionRepository
{
    public function __construct(
        private Cookies $cookies,
        private ClockInterface $clock,
        private CacheItemPoolInterface $cachePool,
        private UuidFactoryInterface $uuidFactory,
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

    public function createPersistentSession(User $user): UserSession
    {
        $session = new UserSession(
            id: $this->uuidFactory->uuid4(),
            expires: $this->clock->now()->add(
                new DateInterval('P1Y'),
            ),
            user: $user,
        );

        $key = $this->getSessionKey($session->id->toString());

        $success = $this->cachePool->save(
            $this->cachePool->getItem($key)
                ->set($session)
                ->expiresAt($session->expires),
        );

        $this->cookies->save(new Cookie(
            name: CookieName::logged_in_session,
            value: $session->id->toString(),
            expire: $session->expires,
        ));

        if (! $success) {
            throw new UnableToSaveSession();
        }

        return $session;
    }

    public function deleteSessionFromCookies(): void
    {
        $cookie = $this->cookies->find(CookieName::logged_in_session);

        if ($cookie === null) {
            return;
        }

        $this->cachePool->deleteItem(
            $this->getSessionKey($cookie->value),
        );

        $this->cookies->delete($cookie);
    }
}
