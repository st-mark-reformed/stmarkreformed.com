<?php

declare(strict_types=1);

namespace App\Contact;

use Psr\Http\Message\ServerRequestInterface;

use function is_array;

readonly class ContactFormDataFactory
{
    public function createFromRequest(
        ServerRequestInterface $request,
    ): ContactFormData {
        $body = $request->getParsedBody();

        $body = is_array($body) ? $body : [];

        return new ContactFormData(
            /** @phpstan-ignore-next-line */
            aPassword: $body['aPassword'] ?? '',
            /** @phpstan-ignore-next-line */
            yourCompany: $body['yourCompany'] ?? '',
            /** @phpstan-ignore-next-line */
            name: $body['name'] ?? '',
            /** @phpstan-ignore-next-line */
            emailAddress: $body['emailAddress'] ?? '',
            /** @phpstan-ignore-next-line */
            message: $body['message'] ?? '',
        );
    }
}
