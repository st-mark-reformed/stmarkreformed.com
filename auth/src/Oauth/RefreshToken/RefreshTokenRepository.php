<?php

declare(strict_types=1);

namespace App\Oauth\RefreshToken;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use Psr\Cache\CacheItemPoolInterface;
use RuntimeException;

use function implode;

readonly class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    public function __construct(
        private CacheItemPoolInterface $cachePool,
    ) {
    }

    public function getNewRefreshToken(): RefreshTokenEntityInterface|null
    {
        return new RefreshTokenEntity();
    }

    public function persistNewRefreshToken(
        RefreshTokenEntityInterface $refreshTokenEntity,
    ): void {
        $success = $this->cachePool->save(
            $this->cachePool->getItem(
                implode('+', [
                    'oauth',
                    'refresh_token',
                    $refreshTokenEntity->getIdentifier(),
                ]),
            )
                ->set($refreshTokenEntity)
                ->expiresAt($refreshTokenEntity->getExpiryDateTime()),
        );

        if ($success) {
            return;
        }

        throw new RuntimeException('Unable to save refresh token');
    }

    public function findTokenById(
        string $tokenId,
    ): RefreshTokenEntityInterface|null {
        $cachedToken = $this->cachePool->getItem(
            implode('+', [
                'oauth',
                'refresh_token',
                $tokenId,
            ]),
        )->get();

        if (! $cachedToken instanceof RefreshTokenEntityInterface) {
            return null;
        }

        return $cachedToken;
    }

    public function revokeRefreshToken(string $tokenId): void
    {
        $this->cachePool->deleteItem(
            implode('+', [
                'oauth',
                'refresh_token',
                $tokenId,
            ]),
        );
    }

    public function isRefreshTokenRevoked(string $tokenId): bool
    {
        return $this->findTokenById($tokenId) === null;
    }
}
