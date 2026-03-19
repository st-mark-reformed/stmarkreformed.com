<?php

declare(strict_types=1);

namespace App\Oauth\Grants;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\RequestTypes\AuthorizationRequestInterface;
use Psr\Http\Message\ServerRequestInterface;

use function array_filter;
use function array_key_exists;
use function count;
use function explode;
use function implode;
use function is_string;
use function trim;

class CustomAuthCodeGrant extends AuthCodeGrant
{
    /**
     * Unfortunately, the parent method checks if `client_id` is set, and if
     * it's not, it returns false, which leads to errors that are hard
     * to track down.
     *
     * @inheritDoc
     */
    public function canRespondToAuthorizationRequest(
        ServerRequestInterface $request,
    ): bool {
        return array_key_exists(
            'response_type',
            $request->getQueryParams(),
        ) && $request->getQueryParams()['response_type'] === 'code';
    }

    /**
     * Unfortunately, some of the errors thrown in the parent method are a
     * little too generic and makes debugging difficult to track down.
     * The whole method will need to be re-implemented, sticking as closely
     * as possible to the original
     *
     * @throws OAuthServerException
     *
     * @inheritDoc
     */
    public function validateAuthorizationRequest(
        ServerRequestInterface $request,
    ): AuthorizationRequestInterface {
        $clientId = $this->getQueryStringParameter(
            'client_id',
            $request,
            $this->getServerParameter(
                'PHP_AUTH_USER',
                $request,
            ),
        );

        if ($clientId === null) {
            throw new OAuthServerException(
                'The client_id parameter is missing.',
                3,
                'invalid_request',
                400,
            );
        }

        $client = $this->getClientEntityOrFail(
            $clientId,
            $request,
        );

        $redirectUri = (string) $this->getQueryStringParameter(
            'redirect_uri',
            $request,
        );

        /**
         * Client must explicitly send redirect_uri. This is a change from the
         * parent method which will fall back to the client redirect URI if
         * there is only 1 specified.
         */
        try {
            $this->validateRedirectUri(
                $redirectUri,
                $client,
                $request,
            );
        } catch (OAuthServerException) {
            throw new OAuthServerException(
                'The redirect_uri parameter is invalid.',
                3,
                'invalid_request',
                400,
            );
        }

        // Now that we've caught the errors here we can invoke the parent
        return parent::validateAuthorizationRequest($request);
    }

    /**
     * Not happy with parent’s error messaging
     *
     * @inheritDoc
     */
    public function validateScopes(
        array|string|null $scopes,
        string|null $redirectUri = null,
    ): array {
        if ($scopes === null) {
            $scopes = [];
        } elseif (is_string($scopes)) {
            $scopes = $this->convertScopesQueryStringToArray($scopes);
        }

        $invalidScopes = [];

        $validScopes = [];

        foreach ($scopes as $scopeItem) {
            $scope = $this->scopeRepository->getScopeEntityByIdentifier(
                /** @phpstan-ignore-next-line */
                $scopeItem,
            );

            if ($scope instanceof ScopeEntityInterface === false) {
                $invalidScopes[] = $scopeItem;

                continue;
            }

            $validScopes[] = $scope;
        }

        if (count($invalidScopes) > 0) {
            $singularPlural = 'scope has';

            if (count($invalidScopes) > 1) {
                $singularPlural = 'scopes have';
            }

            throw new OAuthServerException(
                implode(' ', [
                    'Invalid',
                    $singularPlural,
                    'been requested:',
                    implode(', ', $invalidScopes),
                ]),
                5,
                'invalid_scope',
                400,
            );
        }

        return $validScopes;
    }

    /**
     * Dadgum parent method has private visibility
     *
     * @return string[]
     */
    private function convertScopesQueryStringToArray(string $scopes): array
    {
        return array_filter(
            explode(
                self::SCOPE_DELIMITER_STRING,
                trim($scopes),
            ),
            static fn ($scope) => $scope !== '',
        );
    }
}
