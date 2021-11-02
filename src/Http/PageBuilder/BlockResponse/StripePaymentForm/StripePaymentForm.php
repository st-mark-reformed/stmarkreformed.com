<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\StripePaymentForm;

use App\Http\PageBuilder\BlockResponse\BlockResponseBuilderContract;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\Stripe\StripeFieldHandler;
use App\Templating\TwigControl\TwigControl;
use App\Templating\TwigControl\ViewManager;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;
use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * @psalm-suppress RedundantCastGivenDocblockType
 */
class StripePaymentForm implements BlockResponseBuilderContract
{
    public function __construct(
        private TwigEnvironment $twig,
        private TwigControl $twigControl,
        private ViewManager $viewManager,
        private GenericHandler $genericHandler,
        private StripeFieldHandler $stripeFieldHandler,
    ) {
    }

    /**
     * @throws InvalidConfigException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     * @throws Exception
     *
     * @phpstan-ignore-next-line
     */
    public function buildResponse(MatrixBlock $matrixBlock): string
    {
        $this->twigControl->useCraftTwigLoader();

        $noTopSpace = $this->genericHandler->getBoolean(
            element: $matrixBlock,
            field: 'noTopSpace',
        );

        $paymentFormModels = $this->stripeFieldHandler->getAll(
            element: $matrixBlock,
            field: 'stripePaymentForm',
        );

        $forms = [];

        foreach ($paymentFormModels as $paymentFormModel) {
            $forms[] = new Markup(
                /** @phpstan-ignore-next-line */
                (string) $paymentFormModel->paymentForm(),
                'UTF-8',
            );
        }

        $contentModel = new StripePaymentFormContentModel(
            noTopSpace: $noTopSpace,
            forms: $forms,
        );

        $this->viewManager->unRegisterCssByFileName(
            fileName: 'enupal-button.min.css'
        );

        $this->twigControl->useCustomTwigLoader();

        return $this->twig->render(
            '@app/Http/PageBuilder/BlockResponse/StripePaymentForm/StripePaymentForm.twig',
            ['contentModel' => $contentModel],
        );
    }
}
