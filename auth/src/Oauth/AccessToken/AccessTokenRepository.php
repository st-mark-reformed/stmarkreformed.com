<?php

declare(strict_types=1);

namespace App\Oauth\AccessToken;

use App\Oauth\Client\ClientEntity;
use App\Oauth\OauthPrivateKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Clock\ClockInterface;
use Ramsey\Uuid\UuidFactoryInterface;
use RuntimeException;

use function implode;

readonly class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    public function __construct(
        private ClockInterface $clock,
        private OauthPrivateKey $oauthPrivateKey,
        private UuidFactoryInterface $uuidFactory,
        private CacheItemPoolInterface $cachePool,
    ) {
    }

    /**
     * @param ClientEntityInterface&ClientEntity $clientEntity
     * @param non-empty-string|null              $userIdentifier
     *
     * @inheritDoc
     * @phpstan-ignore-next-line
     */
    public function getNewToken(
        ClientEntityInterface $clientEntity,
        array $scopes,
        string|null $userIdentifier = null,
    ): AccessTokenEntityInterface {
        return new AccessTokenEntity(
            $this->uuidFactory->uuid4()->toString(),
            $this->oauthPrivateKey,
            $scopes,
            $this->clock->now()->add(
                $clientEntity->accessTokenExpiration(),
            ),
            $userIdentifier,
            $clientEntity,
        );
    }

    public function persistNewAccessToken(
        AccessTokenEntityInterface $accessTokenEntity,
    ): void {
        $success = $this->cachePool->save(
            $this->cachePool->getItem(
                implode('+', [
                    'oauth',
                    'access_token',
                    $accessTokenEntity->getIdentifier(),
                ]),
            )
                ->set($accessTokenEntity)
                ->expiresAt($accessTokenEntity->getExpiryDateTime()),
        );

        if ($success) {
            return;
        }

        throw new RuntimeException('Unable to save access token');
    }

    public function findTokenById(
        string $tokenId,
    ): AccessTokenEntityInterface|null {
        $cachedToken = $this->cachePool->getItem(
            implode('+', [
                'oauth',
                'access_token',
                $tokenId,
            ]),
        )->get();

        if (! $cachedToken instanceof AccessTokenEntityInterface) {
            return null;
        }

        return $cachedToken;
    }

    public function revokeAccessToken(string $tokenId): void
    {
        $this->cachePool->deleteItem(
            implode('+', [
                'oauth',
                'access_token',
                $tokenId,
            ]),
        );
    }

    public function isAccessTokenRevoked(string $tokenId): bool
    {
        return $this->findTokenById($tokenId) === null;
    }
}
