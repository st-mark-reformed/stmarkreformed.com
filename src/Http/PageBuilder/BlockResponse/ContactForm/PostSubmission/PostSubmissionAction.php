<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission;

use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\Responder\ResponderFactory;
use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail\SendEmailFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

use function assert;
use function is_array;

class PostSubmissionAction
{
    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector->post(
            '/contact-form-submission',
            self::class,
        );
    }

    public function __construct(
        private SendEmailFactory $sendEmailFactory,
        private ResponderFactory $responderFactory,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $requestData = $request->getParsedBody();

        assert(is_array($requestData));

        $formValues = new FormValues(
            fromUrl: (string) ($requestData['from_url'] ?? ''),
            redirectUrl: (string) ($requestData['redirect_url'] ?? ''),
            name: (string) ($requestData['your_name'] ?? ''),
            email: (string) ($requestData['your_email'] ?? ''),
            message: (string) ($requestData['message'] ?? ''),
        );

        $result = $this->sendEmailFactory
            ->make(formValues: $formValues)
            ->send(formValues: $formValues);

        return $this->responderFactory->make(result: $result)->respond(
            result: $result,
        );
    }
}
