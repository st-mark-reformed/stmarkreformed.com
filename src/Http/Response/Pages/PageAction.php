<?php

declare(strict_types=1);

namespace App\Http\Response\Pages;

use App\Http\Components\Hero\HeroFactory;
use App\Http\Entities\Meta;
use App\Http\PageBuilder\PageBuilderResponseCompiler;
use BuzzingPixel\SlimBridge\ElementSetRoute\RouteParams;
use craft\elements\db\MatrixBlockQuery;
use craft\elements\Entry;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

use function assert;

class PageAction
{
    public function __construct(
        private TwigEnvironment $twig,
        private RouteParams $routeParams,
        private HeroFactory $heroFactory,
        private ResponseFactoryInterface $responseFactory,
        private PageBuilderResponseCompiler $pageBuilderResponseCompiler,
    ) {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $entry = $this->routeParams->getParam('element');

        assert($entry instanceof Entry);

        $pageBuilderQuery = $entry->getFieldValue('pageBuilder');

        assert($pageBuilderQuery instanceof MatrixBlockQuery);

        /** @var MatrixBlock[] $pageBuilderBlocks */
        $pageBuilderBlocks = $pageBuilderQuery->all();

        $response = $this->responseFactory->createResponse()
            ->withHeader('EnableStaticCache', 'true');

        $response->getBody()->write($this->twig->render(
            '@app/Http/Response/Pages/Page.twig',
            [
                'meta' => new Meta(),
                'hero' => $this->heroFactory->createFromEntry(entry: $entry),
                'content' => $this->pageBuilderResponseCompiler->compile(
                    pageBuilderBlocks: $pageBuilderBlocks,
                ),
            ],
        ));

        return $response;
    }
}
