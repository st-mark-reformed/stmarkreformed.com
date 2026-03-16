<?php

declare(strict_types=1);

namespace App\LogIn;

use Psr\Http\Message\ServerRequestInterface;

use function is_array;
use function is_string;

readonly class PostDataFactory
{
    public function __construct(
        private LogInRedirectUrlFactory $logInRedirectUrlFactory,
    ) {
    }

    public function createFromRequest(ServerRequestInterface $request): PostData
    {
        $redirectUrl = $this->logInRedirectUrlFactory->createFromRequest(
            $request,
        );

        $postData = $request->getParsedBody();
        $postData = is_array($postData) ? $postData : [];

        $email = $postData['email'] ?? '';
        $email = is_string($email) ? $email : '';

        $password = $postData['password'] ?? '';
        $password = is_string($password) ? $password : '';

        return new PostData(
            redirectUrl: $redirectUrl,
            email: $email,
            password: $password,
        );
    }
}
