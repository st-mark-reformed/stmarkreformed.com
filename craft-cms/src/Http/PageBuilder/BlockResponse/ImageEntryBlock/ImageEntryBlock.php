<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ImageEntryBlock;

use App\Http\PageBuilder\BlockResponse\BlockResponseBuilderContract;
use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

class ImageEntryBlock implements BlockResponseBuilderContract
{
    public function __construct(
        private TwigEnvironment $twig,
        private AssetsFieldHandler $assetsFieldHandler,
    ) {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     *
     * @phpstan-ignore-next-line
     */
    public function buildResponse(MatrixBlock $matrixBlock): string
    {
        $image = $this->assetsFieldHandler->getOne(
            element: $matrixBlock,
            field: 'image',
        );

        return $this->twig->render(
            '@app/Http/PageBuilder/BlockResponse/ImageEntryBlock/ImageEntryBlock.twig',
            [
                'contentModel' => new ImageEntryBlockContentModel(
                    imageTitle: (string) $image->title,
                    imageUrl: (string) $image->getUrl(),
                ),
            ],
        );
    }
}
