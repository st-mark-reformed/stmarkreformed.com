<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ImageContentCta;

use App\Http\Components\Link\LinkFactory;
use App\Http\PageBuilder\BlockResponse\BlockResponseBuilderContract;
use craft\elements\Asset;
use craft\elements\db\AssetQuery;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use percipioglobal\colourswatches\models\ColourSwatches;
use stdClass;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;
use typedlinkfield\models\Link as LinkFieldModel;
use yii\base\InvalidConfigException;

use function assert;
use function is_array;
use function property_exists;

class ImageContentCta implements BlockResponseBuilderContract
{
    public function __construct(
        private TwigEnvironment $twig,
        private LinkFactory $linkFactory,
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
        $backgroundColorModel = $matrixBlock->getFieldValue(
            'backgroundColor'
        );

        assert($backgroundColorModel instanceof ColourSwatches);

        $backgroundColors = $backgroundColorModel->colors();

        assert(is_array($backgroundColors));

        $backgroundColorOption = $backgroundColors[0];

        assert($backgroundColorOption instanceof stdClass);

        assert(property_exists(
            $backgroundColorOption,
            'tailwindColor'
        ));

        $backgroundColor = (string) $backgroundColorOption->tailwindColor;

        $imageQuery = $matrixBlock->getFieldValue('image');

        assert($imageQuery instanceof AssetQuery);

        $image = $imageQuery->one();

        assert($image instanceof Asset);

        $contentTwigMarkup = $matrixBlock->getFieldValue(
            'contentField'
        );

        assert($contentTwigMarkup instanceof Markup);

        $ctaLinkFieldModel = $matrixBlock->getFieldValue('cta');

        assert($ctaLinkFieldModel instanceof LinkFieldModel);

        return $this->twig->render(
            '@app/Http/PageBuilder/BlockResponse/ImageContentCta/ImageContentCta.twig',
            [
                'contentModel' => new ImageContentCtaContentModel(
                    tailwindBackgroundColor: $backgroundColor,
                    imageUrl: (string) $image->getUrl(),
                    showTealOverlayOnImage: (bool) $matrixBlock->getFieldValue(
                        'showTealOverlayOnImage',
                    ),
                    preHeadline: (string) $matrixBlock->getFieldValue(
                        'preHeadline',
                    ),
                    headline: (string) $matrixBlock->getFieldValue(
                        'headline'
                    ),
                    content: $contentTwigMarkup,
                    cta: $this->linkFactory->fromLinkFieldModel(
                        linkFieldModel: $ctaLinkFieldModel
                    ),
                ),
            ],
        );
    }
}
