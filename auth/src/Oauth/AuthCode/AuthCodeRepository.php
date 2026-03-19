<?php

declare(strict_types=1);

namespace App\Oauth\AuthCode;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use Psr\Cache\CacheItemPoolInterface;
use RuntimeException;

use function implode;

readonly class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    public function __construct(
        private CacheItemPoolInterface $cachePool,
    ) {
    }

    public function getNewAuthCode(): AuthCodeEntityInterface
    {
        return new AuthCodeEntity();
    }

    public function persistNewAuthCode(
        AuthCodeEntityInterface $authCodeEntity,
    ): void {
        $success = $this->cachePool->save(
            $this->cachePool->getItem(
                implode('+', [
                    'oauth',
                    'auth_code',
                    $authCodeEntity->getIdentifier(),
                ]),
            )
                ->set($authCodeEntity)
                ->expiresAt($authCodeEntity->getExpiryDateTime()),
        );

        if ($success) {
            return;
        }

        throw new RuntimeException('Unable to save auth code');
    }

    public function findCodeById(
        string $codeId,
    ): AuthCodeEntityInterface|null {
        $cachedCode = $this->cachePool->getItem(
            implode('+', [
                'oauth',
                'auth_code',
                $codeId,
            ]),
        )->get();

        if (! $cachedCode instanceof AuthCodeEntityInterface) {
            return null;
        }

        return $cachedCode;
    }

    public function revokeAuthCode(string $codeId): void
    {
        $this->cachePool->deleteItem(
            implode('+', [
                'oauth',
                'auth_code',
                $codeId,
            ]),
        );
    }

    public function isAuthCodeRevoked(string $codeId): bool
    {
        return $this->findCodeById($codeId) === null;
    }
}
