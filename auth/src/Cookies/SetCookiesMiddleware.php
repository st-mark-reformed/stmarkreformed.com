<?php

declare(strict_types=1);

namespace App\Cookies;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Random\RandomException;
use SodiumException;

use function base64_encode;
use function gmdate;
use function implode;
use function json_encode;
use function random_bytes;
use function sodium_crypto_secretbox;

use const SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;

readonly class SetCookiesMiddleware implements MiddlewareInterface
{
    public function __construct(private Cookies $cookies)
    {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $response = $handler->handle($request);

        $response = $this->setAddedCookies($response);

        return $this->setDeletedCookies($response);
    }

    private function setAddedCookies(
        ResponseInterface $response,
    ): ResponseInterface {
        foreach ($this->cookies->getAddedCookies() as $cookie) {
            $response = $this->setAddedCookie(
                $response,
                $cookie,
            );
        }

        return $response;
    }

    /**
     * @throws RandomException
     * @throws SodiumException
     */
    private function setAddedCookie(
        ResponseInterface $response,
        Cookie $cookie,
    ): ResponseInterface {
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        $secretBox = sodium_crypto_secretbox(
            $cookie->asString(),
            $nonce,
            $this->cookies->getEncryptionKey(),
        );

        $setValue = [
            implode('=', [
                $cookie->name->name,
                json_encode([
                    'nonce' => base64_encode($nonce),
                    'value' => base64_encode($secretBox),
                ]),
            ]),
        ];

        if ($cookie->expire !== null) {
            $setValue[] = implode('=', [
                'Expires',
                gmdate(
                    'D, d M Y H:i:s T',
                    $cookie->expire->getTimestamp(),
                ),
            ]);
        }

        if ($cookie->path !== '') {
            $setValue[] = implode('=', [
                'Path',
                $cookie->path,
            ]);
        }

        if ($cookie->domain !== '') {
            $setValue[] = implode('=', [
                'Domain',
                $cookie->domain,
            ]);
        }

        if ($cookie->secure) {
            $setValue[] = 'Secure';
        }

        if ($cookie->httpOnly) {
            $setValue[] = 'HttpOnly';
        }

        return $response->withAddedHeader(
            'Set-Cookie',
            implode('; ', $setValue),
        );
    }

    private function setDeletedCookies(
        ResponseInterface $response,
    ): ResponseInterface {
        foreach ($this->cookies->getDeletedCookieKeys() as $cookie) {
            $response = $this->setDeletedCookie(
                $response,
                $cookie,
            );
        }

        return $response;
    }

    private function setDeletedCookie(
        ResponseInterface $response,
        CookieName $cookieName,
    ): ResponseInterface {
        $cookie = $this->cookies->find($cookieName) ?? new Cookie(
            $cookieName,
            '',
        );

        $setValue = [
            implode('=', [
                $cookie->name->name,
                '',
            ]),
            implode('=', [
                'Expires',
                'Thu, 01 Jan 1970 00:00:00 GMT',
            ]),
        ];

        if ($cookie->path !== '') {
            $setValue[] = implode('=', [
                'Path',
                $cookie->path,
            ]);
        }

        if ($cookie->domain !== '') {
            $setValue[] = implode('=', [
                'Domain',
                $cookie->domain,
            ]);
        }

        if ($cookie->secure) {
            $setValue[] = 'Secure';
        }

        if ($cookie->httpOnly) {
            $setValue[] = 'HttpOnly';
        }

        return $response->withAddedHeader(
            'Set-Cookie',
            implode('; ', $setValue),
        );
    }
}
