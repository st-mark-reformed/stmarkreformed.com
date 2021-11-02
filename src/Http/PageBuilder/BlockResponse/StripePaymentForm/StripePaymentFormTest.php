<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\StripePaymentForm;

use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\Stripe\StripeFieldHandler;
use App\Templating\TwigControl\TwigControl;
use App\Templating\TwigControl\ViewManager;
use craft\base\Element;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use enupal\stripe\elements\PaymentForm;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;
use yii\base\Exception;
use yii\base\InvalidConfigException;

use function array_pop;
use function assert;

/**
 * @psalm-suppress MixedArgument
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedAssignment
 * @psalm-suppress PropertyNotSetInConstructor
 */
class StripePaymentFormTest extends TestCase
{
    private StripePaymentForm $service;

    /** @var mixed[] */
    private array $calls = [];

    /**
     * @var MatrixBlock&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $matrixBlock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->matrixBlock = $this->createMock(
            MatrixBlock::class,
        );

        $this->service = new StripePaymentForm(
            twig: $this->mockTwig(),
            twigControl: $this->mockTwigControl(),
            viewManager: $this->mockViewManager(),
            genericHandler: $this->mockGenericHandler(),
            stripeFieldHandler: $this->mockStripeFieldHandler(),
        );
    }

    /**
     * @return MockObject&TwigEnvironment
     */
    private function mockTwig(): mixed
    {
        $twig = $this->createMock(TwigEnvironment::class);

        $twig->method('render')->willReturnCallback(
            function (string $name, array $context): string {
                $this->calls[] = [
                    'object' => 'TwigEnvironment',
                    'method' => 'render',
                    'name' => $name,
                    'context' => $context,
                ];

                return 'twigRenderResponse';
            }
        );

        return $twig;
    }

    /**
     * @return MockObject&TwigControl
     */
    private function mockTwigControl(): mixed
    {
        $twigControl = $this->createMock(TwigControl::class);

        $twigControl->method('useCraftTwigLoader')
            ->willReturnCallback(function (): void {
                $this->calls[] = [
                    'object' => 'TwigControl',
                    'method' => 'useCraftTwigLoader',
                ];
            });

        $twigControl->method('useCustomTwigLoader')
            ->willReturnCallback(function (): void {
                $this->calls[] = [
                    'object' => 'TwigControl',
                    'method' => 'useCustomTwigLoader',
                ];
            });

        return $twigControl;
    }

    /**
     * @return MockObject&ViewManager
     */
    private function mockViewManager(): mixed
    {
        $viewManager = $this->createMock(ViewManager::class);

        $viewManager->method('unRegisterCssByFileName')
            ->willReturnCallback(function (string $fileName): void {
                $this->calls[] = [
                    'object' => 'ViewManager',
                    'method' => 'unRegisterCssByFileName',
                    'fileName' => $fileName,
                ];
            });

        return $viewManager;
    }

    /**
     * @return MockObject&GenericHandler
     */
    private function mockGenericHandler(): mixed
    {
        $handler = $this->createMock(GenericHandler::class);

        $handler->method('getBoolean')->willReturnCallback(
            function (Element $element, string $field): bool {
                $this->calls[] = [
                    'object' => 'GenericHandler',
                    'method' => 'getBoolean',
                    'element' => $element,
                    'field' => $field,
                ];

                return true;
            }
        );

        return $handler;
    }

    /**
     * @return MockObject&StripeFieldHandler
     */
    private function mockStripeFieldHandler(): mixed
    {
        $handler = $this->createMock(
            StripeFieldHandler::class,
        );

        $form1 = $this->createMock(PaymentForm::class);

        $form1->method('paymentForm')->willReturnCallback(
            function (): Markup {
                $this->calls[] = [
                    'object' => 'PaymentForm1',
                    'method' => 'paymentForm',
                ];

                return new Markup(
                    'form1Markup',
                    'UTF-8',
                );
            }
        );

        $form2 = $this->createMock(PaymentForm::class);

        $form2->method('paymentForm')->willReturnCallback(
            function (): Markup {
                $this->calls[] = [
                    'object' => 'PaymentForm2',
                    'method' => 'paymentForm',
                ];

                return new Markup(
                    'form2Markup',
                    'UTF-8',
                );
            }
        );

        $handler->method('getAll')->willReturnCallback(
            function (
                Element $element,
                string $field
            ) use (
                $form1,
                $form2,
            ): array {
                $this->calls[] = [
                    'object' => 'StripeFieldHandler',
                    'method' => 'getAll',
                    'element' => $element,
                    'field' => $field,
                ];

                return [
                    $form1,
                    $form2,
                ];
            }
        );

        return $handler;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function testBuildResponse(): void
    {
        self::assertSame(
            'twigRenderResponse',
            $this->service->buildResponse(
                matrixBlock: $this->matrixBlock,
            ),
        );

        $calls = $this->calls;

        $lastCall = array_pop($calls);

        self::assertSame(
            [
                [
                    'object' => 'TwigControl',
                    'method' => 'useCraftTwigLoader',
                ],
                [
                    'object' => 'GenericHandler',
                    'method' => 'getBoolean',
                    'element' => $this->matrixBlock,
                    'field' => 'noTopSpace',
                ],
                [
                    'object' => 'StripeFieldHandler',
                    'method' => 'getAll',
                    'element' => $this->matrixBlock,
                    'field' => 'stripePaymentForm',
                ],
                [
                    'object' => 'PaymentForm1',
                    'method' => 'paymentForm',
                ],
                [
                    'object' => 'PaymentForm2',
                    'method' => 'paymentForm',
                ],
                [
                    'object' => 'ViewManager',
                    'method' => 'unRegisterCssByFileName',
                    'fileName' => 'enupal-button.min.css',
                ],
                [
                    'object' => 'TwigControl',
                    'method' => 'useCustomTwigLoader',
                ],
            ],
            $calls,
        );

        self::assertCount(4, $lastCall);

        self::assertSame(
            'TwigEnvironment',
            $lastCall['object'],
        );

        self::assertSame(
            'render',
            $lastCall['method'],
        );

        self::assertSame(
            '@app/Http/PageBuilder/BlockResponse/StripePaymentForm/StripePaymentForm.twig',
            $lastCall['name'],
        );

        $context = $lastCall['context'];

        self::assertCount(1, $context);

        $contentModel = $context['contentModel'];

        assert($contentModel instanceof StripePaymentFormContentModel);

        $forms = $contentModel->forms();

        self::assertTrue($contentModel->noTopSpace());

        self::assertCount(2, $forms);

        self::assertSame(
            'form1Markup',
            (string) $forms[0],
        );

        self::assertSame(
            'form2Markup',
            (string) $forms[1],
        );
    }
}
