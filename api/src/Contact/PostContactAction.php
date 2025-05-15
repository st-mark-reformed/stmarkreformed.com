<?php

declare(strict_types=1);

namespace App\Contact;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

readonly class PostContactAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post('/contact', self::class);
    }

    public function __construct(
        private SendEmail $sendEmail,
        private PostContactFormResponder $responder,
        private ContactFormDataFactory $contactFormDataFactory,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $data = $this->contactFormDataFactory->createFromRequest(
            $request,
        );

        $result = $this->sendEmail->send($data);

        return $this->responder->respond($result);
    }
}
