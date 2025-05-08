<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\LatestNews;

use App\Http\PageBuilder\BlockResponse\BlockResponseBuilderContract;
use App\Http\PageBuilder\BlockResponse\LatestNews\Entities\LatestNewsContentModel;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

class LatestNews implements BlockResponseBuilderContract
{
    public function __construct(
        private TwigEnvironment $twig,
        private LatestNewsRetriever $newsRetriever,
    ) {
    }

    /**
     * @throws SyntaxError
     * @throws InvalidConfigException
     * @throws InvalidFieldException
     * @throws RuntimeError
     * @throws LoaderError
     *
     * @phpstan-ignore-next-line
     */
    public function buildResponse(MatrixBlock $matrixBlock): string
    {
        return $this->twig->render(
            '@app/Http/PageBuilder/BlockResponse/LatestNews/LatestNews.twig',
            [
                'contentModel' => new LatestNewsContentModel(
                    heading: (string) $matrixBlock->getFieldValue(
                        'heading',
                    ),
                    subHeading: (string) $matrixBlock->getFieldValue(
                        'subHeading',
                    ),
                    newsItems: $this->newsRetriever->retrieve(),
                ),
            ],
        );
    }
}
