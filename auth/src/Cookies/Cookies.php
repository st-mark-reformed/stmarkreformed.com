<?php

declare(strict_types=1);

namespace App\Cookies;

use DateTimeImmutable;
use Throwable;

use function array_map;
use function base64_decode;
use function is_array;
use function is_int;
use function json_decode;
use function sodium_crypto_secretbox_open;
use function strlen;

use const SODIUM_CRYPTO_SECRETBOX_KEYBYTES;

class Cookies
{
    /**
     * @param array<string, string> $cookies
     * @param array<string, Cookie> $addedCookies
     * @param CookieName[]          $deletedCookieKeys
     * @param non-empty-string      $encryptionKey
     *
     * @throws CookieEncryptionKeyException
     */
    public function __construct(
        private readonly array $cookies,
        private array &$addedCookies,
        private array &$deletedCookieKeys,
        private readonly string $encryptionKey,
    ) {
        if (
            $encryptionKey === '' ||
            strlen($encryptionKey) !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES
        ) {
            throw new CookieEncryptionKeyException();
        }
    }

    public function getEncryptionKey(): string
    {
        return $this->encryptionKey;
    }

    /** @return array<string, Cookie> $addedCookies */
    public function getAddedCookies(): array
    {
        return $this->addedCookies;
    }

    /** @return CookieName[] */
    public function getDeletedCookieKeys(): array
    {
        return $this->deletedCookieKeys;
    }

    public function findOrEmpty(CookieName $name): Cookie
    {
        $cookie = $this->find($name);

        return $cookie ?? new Cookie($name, '');
    }

    public function find(CookieName $name): Cookie|null
    {
        if (isset($this->addedCookies[$name->name])) {
            return $this->addedCookies[$name->name];
        }

        $cookieActual = $this->cookies[$name->name] ?? null;

        if ($cookieActual === null) {
            return null;
        }

        $cookieDecode = json_decode($cookieActual, true);

        if (! is_array($cookieDecode)) {
            return null;
        }

        try {
            /** @phpstan-ignore-next-line */
            $nonce = base64_decode($cookieDecode['nonce'], true);

            $value = json_decode(
                /** @phpstan-ignore-next-line */
                sodium_crypto_secretbox_open(
                    /** @phpstan-ignore-next-line */
                    base64_decode($cookieDecode['value']),
                    /** @phpstan-ignore-next-line */
                    $nonce,
                    $this->encryptionKey,
                ),
                true,
            );

            if (! is_array($value)) {
                return null;
            }

            $expire = null;

            if (is_int($value['expire'])) {
                $expire = new DateTimeImmutable()
                    ->setTimestamp($value['expire']);
            }

            return new Cookie(
                /** @phpstan-ignore-next-line */
                CookieName::fromString($value['name'] ?? ''),
                /** @phpstan-ignore-next-line */
                $value['value'],
                $expire,
                /** @phpstan-ignore-next-line */
                $value['path'],
                /** @phpstan-ignore-next-line */
                $value['domain'],
                /** @phpstan-ignore-next-line */
                $value['secure'],
                /** @phpstan-ignore-next-line */
                $value['httpOnly'],
            );
        } catch (Throwable) {
            return null;
        }
    }

    public function save(Cookie $cookie): void
    {
        $this->addedCookies[$cookie->name->name] = $cookie;
    }

    public function delete(Cookie|CookieName $cookie): void
    {
        if ($cookie instanceof Cookie) {
            $cookie = $cookie->name;
        }

        if (isset($this->addedCookies[$cookie->name])) {
            unset($this->addedCookies[$cookie->name]);
        }

        $this->deletedCookieKeys[] = $cookie;
    }

    /** @param array<Cookie|CookieName> $cookies */
    public function deleteCookies(array $cookies): void
    {
        array_map(
            function (Cookie|CookieName $cookie): void {
                $this->delete($cookie);
            },
            $cookies,
        );
    }
}
