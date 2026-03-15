<?php

declare(strict_types=1);

namespace App\Cookies;

use DateTimeImmutable;

use function json_encode;

readonly class Cookie
{
    public function __construct(
        public CookieName $name,
        public string $value,
        public DateTimeImmutable|null $expire = null,
        public string $path = '/',
        public string $domain = '',
        public bool $secure = true,
        public bool $httpOnly = true,
    ) {
    }

    public function __toString(): string
    {
        return $this->asString();
    }

    /**
     * @return array{
     *     name: string,
     *     value: string,
     *     expire: int|null,
     *     path: string,
     *     domain: string,
     *     secure: bool,
     *     httpOnly: bool,
     * }
     */
    public function asArray(): array
    {
        return [
            'name' => $this->name->name,
            'value' => $this->value,
            'expire' => $this->expire?->getTimestamp(),
            'path' => $this->path,
            'domain' => $this->domain,
            'secure' => $this->secure,
            'httpOnly' => $this->httpOnly,
        ];
    }

    public function asString(): string
    {
        return (string) json_encode($this->asArray());
    }
}
