<?php

declare(strict_types=1);

namespace App\Http\Response\Error;

use App\Http\Components\Hero\HeroFactory;
use App\Http\Entities\Meta;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

class Error500Responder
{
    public function __construct(
        private TwigEnvironment $twig,
        private LoggerInterface $logger,
        private HeroFactory $heroFactory,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function respond(Throwable $exception): ResponseInterface
    {
        $this->logger->error(
            'An exception was thrown',
            ['exception' => $exception],
        );

        $response = $this->responseFactory->createResponse(
            500,
            'An internal server error occurred',
        );

        $response->getBody()->write(
            $this->twig->render(
                '@app/Http/Response/Error/Error500.twig',
                [
                    'meta' => new Meta(
                        metaTitle: 'Internal Server Error',
                    ),
                    'hero' => $this->heroFactory->createFromDefaults(),
                ],
            )
        );

        return $response;
    }
}
