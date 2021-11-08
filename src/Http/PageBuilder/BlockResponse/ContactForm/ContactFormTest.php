<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm;

use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\FormValues;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\LinkField\LinkFieldHandler;
use BuzzingPixel\SlimBridge\ServerRequestFactory;
use craft\base\Element;
use craft\elements\MatrixBlock;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment as TwigEnvironment;
use Twig\Markup;
use typedlinkfield\models\Link as LinkFieldModel;

use function array_pop;
use function assert;
use function is_array;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArgument
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedAssignment
 */
class ContactFormTest extends TestCase
{
    private ContactForm $contactForm;

    /** @var mixed[] */
    private array $calls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->contactForm = new ContactForm(
            twig: $this->mockTwig(),
            genericHandler: $this->mockGenericHandler(),
            linkFieldHandler: $this->mockLinkFieldHandler(),
            requestFactory: $this->mockRequestFactory(),
            messageRetriever: $this->mockMessageRetriever(),
        );
    }

    /**
     * @return MockObject&TwigEnvironment
     */
    private function mockTwig(): mixed
    {
        $twig = $this->createMock(
            TwigEnvironment::class,
        );

        $twig->method('render')->willReturnCallback(
            function (string $name, array $context): string {
                $this->calls[] = [
                    'object' => 'TwigEnvironment',
                    'method' => 'render',
                    'name' => $name,
                    'context' => $context,
                ];

                return 'twigRenderReturn';
            }
        );

        return $twig;
    }

    /**
     * @return MockObject&GenericHandler
     */
    private function mockGenericHandler(): mixed
    {
        $genericHandler = $this->createMock(
            GenericHandler::class,
        );

        $genericHandler->method('getTwigMarkup')->willReturnCallback(
            function (Element $element, string $field): Markup {
                $this->calls[] = [
                    'object' => 'GenericHandler',
                    'method' => 'getTwigMarkup',
                    'element' => $element,
                    'field' => $field,
                ];

                return new Markup(
                    'testTwigMarkupString',
                    'UTF-8',
                );
            }
        );

        return $genericHandler;
    }

    /**
     * @return MockObject&LinkFieldHandler
     */
    private function mockLinkFieldHandler(): mixed
    {
        $handler = $this->createMock(
            LinkFieldHandler::class,
        );

        $handler->method('getModel')->willReturnCallback(
            function (Element $element, string $field): LinkFieldModel {
                $this->calls[] = [
                    'object' => 'LinkFieldHandler',
                    'method' => 'getModel',
                    'element' => $element,
                    'field' => $field,
                ];

                $model = $this->createMock(
                    LinkFieldModel::class,
                );

                $model->method('getUrl')->willReturn(
                    'testLinkUrl',
                );

                return $model;
            }
        );

        return $handler;
    }

    /**
     * @return MockObject&ServerRequestFactory
     */
    private function mockRequestFactory(): mixed
    {
        $request = $this->createMock(
            ServerRequestInterface::class,
        );

        $request->method('getUri')->willReturn(
            'testFromUrl'
        );

        $factory = $this->createMock(
            ServerRequestFactory::class,
        );

        $factory->method('make')->willReturn($request);

        return $factory;
    }

    /**
     * @return MockObject&MessageRetriever
     */
    private function mockMessageRetriever(): mixed
    {
        $formValues = $this->createMock(
            FormValues::class,
        );

        $formValues->method('nameRaw')
            ->willReturn('testName');

        $service = $this->createMock(
            MessageRetriever::class,
        );

        $service->method('retrieveFormValuesFromMessage')
            ->willReturn($formValues);

        return $service;
    }

    public function testBuildResponse(): void
    {
        $matrixBlock = $this->createMock(
            MatrixBlock::class,
        );

        $response = $this->contactForm->buildResponse(
            matrixBlock: $matrixBlock,
        );

        self::assertSame(
            'twigRenderReturn',
            $response,
        );

        $callsExceptLast = $this->calls;

        $lastCall = array_pop($callsExceptLast);

        self::assertSame(
            [
                [
                    'object' => 'GenericHandler',
                    'method' => 'getTwigMarkup',
                    'element' => $matrixBlock,
                    'field' => 'contentField',
                ],
                [
                    'object' => 'LinkFieldHandler',
                    'method' => 'getModel',
                    'element' => $matrixBlock,
                    'field' => 'successRedirect',
                ],
            ],
            $callsExceptLast,
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
            '@app/Http/PageBuilder/BlockResponse/ContactForm/ContactForm.twig',
            $lastCall['name'],
        );

        $context = $lastCall['context'];

        assert(is_array($context));

        $contentModel = $context['contentModel'];

        assert($contentModel instanceof ContactFormContentModel);

        self::assertSame(
            'testTwigMarkupString',
            (string) $contentModel->content(),
        );

        self::assertSame(
            'testFromUrl',
            $contentModel->fromUrl(),
        );

        self::assertSame(
            'testLinkUrl',
            $contentModel->redirectUrl(),
        );

        $formValues = $contentModel->formValues();

        assert($formValues instanceof FormValues);

        self::assertSame(
            'testName',
            $formValues->nameRaw(),
        );
    }
}
