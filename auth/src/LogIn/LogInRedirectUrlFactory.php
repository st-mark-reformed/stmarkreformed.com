<?php

declare(strict_types=1);

namespace App\LogIn;

use App\Url\AppUrlFactory;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

use function implode;
use function is_array;
use function is_string;
use function parse_url;

readonly class LogInRedirectUrlFactory
{
    public function __construct(private AppUrlFactory $appUrlFactory)
    {
    }

    public function createFromRequest(ServerRequestInterface $request): string
    {
        $data = $request->getMethod() === 'GET' ?
            $request->getQueryParams() :
            $request->getParsedBody();

        $data = is_array($data) ? $data : [];

        $redirectUrl = $data['redirect_url'] ?? '';
        $redirectUrl = is_string($redirectUrl) ? $redirectUrl : '';

        $parts = parse_url($redirectUrl);
        $parts = is_array($parts) ? $parts : [];

        $base = implode('', [
            $parts['scheme'] ?? '',
            '://',
            $parts['host'] ?? '',
            '/',
        ]);

        if ($base !== $this->appUrlFactory->create()->asString()) {
            throw new RuntimeException(
                'The redirect URL is not allowed',
            );
        }

        return $redirectUrl;
    }
}
