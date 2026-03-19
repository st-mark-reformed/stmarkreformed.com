<?php

declare(strict_types=1);

namespace App\Oauth\Scopes;

use Composer\ClassMapGenerator\ClassMapGenerator;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Psr\Container\ContainerInterface;
use RuntimeException;

use function array_filter;
use function array_find;
use function array_keys;
use function array_map;
use function array_values;

readonly class ScopeRepository implements ScopeRepositoryInterface
{
    /** @var ScopeEntityInterface[] */
    private array $entities;

    public function __construct(ContainerInterface $di)
    {
        $generator = new ClassMapGenerator();
        $generator->scanPaths(__DIR__ . '/Entities');

        $classStrings = array_keys($generator->getClassMap()->map);

        $this->entities = array_map(
            static function (string $classString) use ($di) {
                $scopeEntity = $di->get($classString);

                if (! ($scopeEntity instanceof ScopeEntityInterface)) {
                    throw new RuntimeException(
                        'Not instance of ScopeEntityInterface',
                    );
                }

                return $scopeEntity;
            },
            $classStrings,
        );
    }

    public function getScopeEntityByIdentifier(
        string $identifier,
    ): ScopeEntityInterface|null {
        return array_find(
            $this->entities,
            static fn (
                ScopeEntityInterface $e,
            ) => $e->getIdentifier() === $identifier,
        );
    }

    /**
     * @param ScopeEntityInterface[] $scopes
     *
     * @return ScopeEntityInterface[]
     */
    public function finalizeScopes(
        array $scopes,
        string $grantType,
        ClientEntityInterface $clientEntity,
        string|null $userIdentifier = null,
        string|null $authCodeId = null,
    ): array {
        return array_values(array_filter(
            $scopes,
            function (ScopeEntityInterface $scope) {
                $systemScope = $this->getScopeEntityByIdentifier(
                    $scope->getIdentifier(),
                );

                return $systemScope !== null;
            },
        ));
    }
}
