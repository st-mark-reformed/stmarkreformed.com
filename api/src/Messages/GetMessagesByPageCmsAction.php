<?php

declare(strict_types=1);

namespace App\Messages;

use App\Authentication\RequireCmsAccessRoleMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;
use Slim\Exception\HttpNotFoundException;

use function is_numeric;
use function json_encode;

readonly class GetMessagesByPageCmsAction
{
    public const int LIMIT = 25;

    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/cms/entries/messages/page/{pageNum}',
            self::class,
        )
            ->add(RequireCmsAccessRoleMiddleware::class)
            ->add(RequireOauthTokenHeaderMiddleware::class);
    }

    public function __construct(private MessageRepository $repository)
    {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $pageNum = $request->getAttribute('pageNum');

        if (! is_numeric($pageNum)) {
            throw new HttpNotFoundException($request);
        }

        $pageNum = (int) $pageNum;

        $results = $this->repository->findAllByLimit(
            offset: ($pageNum - 1) * self::LIMIT,
            limit: self::LIMIT,
            publishStatus: PublishStatusOption::PUBLISHED,
        );

        if ($pageNum > 1 && $results->messages->count() < 1) {
            throw new HttpNotFoundException($request);
        }

        $response->getBody()->write((string) json_encode(
            $results->asScalarWithPageData($pageNum),
        ));

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }
}
