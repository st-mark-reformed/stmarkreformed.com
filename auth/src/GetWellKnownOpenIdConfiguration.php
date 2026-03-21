<?php

declare(strict_types=1);

namespace App;

use App\Url\AppUrlFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

use function json_encode;

readonly class GetWellKnownOpenIdConfiguration
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/.well-known/openid-configuration',
            self::class,
        );

        $routes->get(
            '/well-known/openid-configuration',
            self::class,
        );
    }

    public function __construct(private AppUrlFactory $appUrlFactory)
    {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $response->getBody()->write((string) json_encode([
            'issuer' => $this->appUrlFactory->create()->asString(),
            'authorization_endpoint' => $this->appUrlFactory->create([
                'oauth',
                'authorize',
            ])->asString(),
            'token_endpoint' => $this->appUrlFactory->create([
                'oauth',
                'token',
            ])->asString(),
            // 'device_authorization_endpoint' => 'TODO',
            'userinfo_endpoint' => $this->appUrlFactory->create(
                'userinfo',
            )->asString(),
            // 'mfa_challenge_endpoint' => 'TODO',
            // 'jwks_uri' => 'TODO',
            // 'registration_endpoint' => 'TODO',
            // 'revocation_endpoint' => 'TODO',
            'scopes_supported' => [
                'openid',
                'profile',
                'offline_access',
                'name',
                'given_name',
                'family_name',
                'email',
                'email_verified',
            ],
            'response_types_supported' => [
                'code',
                'token',
                'id_token',
                'code token',
                'code id_token',
                'token id_token',
                'code token id_token',
            ],
            'code_challenge_methods_supported' => [
                'S256',
                'plain',
            ],
            'response_modes_supported' => ['query'],
            'subject_types_supported' => ['public'],
            'token_endpoint_auth_methods_supported' => [
                'client_secret_basic',
                'client_secret_post',
                'private_key_jwt',
            ],
            'token_endpoint_auth_signing_alg_values_supported' => ['RS256'],
            'claims_supported' => [
                'aud',
                'auth_time',
                'created_at',
                'email',
                'email_verified',
                'exp',
                'family_name',
                'given_name',
                'iat',
                'identities',
                'iss',
                'name',
                'nickname',
                'phone_number',
                'picture',
                'sub',
            ],
            'request_uri_parameter_supported' => false,
            'request_parameter_supported' => false,
            'id_token_signing_alg_values_supported' => [
                'HS256',
                'RS256',
            ],
            // 'backchannel_logout_supported' => true,
            // 'backchannel_logout_session_supported' => true,
            // 'backchannel_authentication_endpoint' => 'TODO',
            // 'backchannel_token_delivery_modes_supported' => ['poll'],
            // 'global_token_revocation_endpoint' => 'TODO',
            // 'global_token_revocation_endpoint_auth_methods_supported' => [
            //     'global-token-revocation+jwt',
            // ],
        ]));

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }
}
