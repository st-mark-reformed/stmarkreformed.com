<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ImageContentCta;

use App\Http\Components\Link\LinkFactory;
use App\Http\PageBuilder\BlockResponse\BlockResponseBuilderContract;
use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use App\Shared\FieldHandlers\ColourSwatches\ColorOptionFromElementField;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\LinkField\LinkFieldHandler;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

class ImageContentCta implements BlockResponseBuilderContract
{
    public function __construct(
        private TwigEnvironment $twig,
        private LinkFactory $linkFactory,
        private GenericHandler $genericHandler,
        private LinkFieldHandler $linkFieldHandler,
        private AssetsFieldHandler $assetsFieldHandler,
        private ColorOptionFromElementField $colorOptionFromElementField,
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
        $backgroundColor = $this->colorOptionFromElementField->getStringValue(
            element: $matrixBlock,
            fieldName: 'backgroundColor',
            option: 'tailwindColor',
        );

        $image = $this->assetsFieldHandler->getOne(
            element: $matrixBlock,
            field: 'image',
        );

        return $this->twig->render(
            '@app/Http/PageBuilder/BlockResponse/ImageContentCta/ImageContentCta.twig',
            [
                'contentModel' => new ImageContentCtaContentModel(
                    tailwindBackgroundColor: $backgroundColor,
                    contentDisposition: $this->genericHandler->getString(
                        element: $matrixBlock,
                        field: 'contentDisposition',
                    ),
                    imageUrl: (string) $image->getUrl(),
                    imageAltText:(string) $image->title,
                    showTealOverlayOnImage: $this->genericHandler->getBoolean(
                        element: $matrixBlock,
                        field: 'showTealOverlayOnImage',
                    ),
                    preHeadline: $this->genericHandler->getString(
                        element: $matrixBlock,
                        field: 'preHeadline',
                    ),
                    headline: $this->genericHandler->getString(
                        element: $matrixBlock,
                        field: 'headline',
                    ),
                    content: $this->genericHandler->getTwigMarkup(
                        element: $matrixBlock,
                        field: 'contentField',
                    ),
                    cta: $this->linkFactory->fromLinkFieldModel(
                        linkFieldModel: $this->linkFieldHandler->getModel(
                            element: $matrixBlock,
                            field: 'cta',
                        ),
                    ),
                ),
            ],
        );
    }
}
