<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm;

use App\Http\PageBuilder\BlockResponse\BlockResponseBuilderContract;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\LinkField\LinkFieldHandler;
use BuzzingPixel\SlimBridge\ServerRequestFactory;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ContactForm implements BlockResponseBuilderContract
{
    public function __construct(
        private TwigEnvironment $twig,
        private GenericHandler $genericHandler,
        private LinkFieldHandler $linkFieldHandler,
        private ServerRequestFactory $requestFactory,
        private MessageRetriever $messageRetriever,
    ) {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     *
     * @phpstan-ignore-next-line
     */
    public function buildResponse(MatrixBlock $matrixBlock): string
    {
        $content = $this->genericHandler->getTwigMarkup(
            element: $matrixBlock,
            field: 'contentField',
        );

        $linkModel = $this->linkFieldHandler->getModel(
            element: $matrixBlock,
            field: 'successRedirect',
        );

        return $this->twig->render(
            '@app/Http/PageBuilder/BlockResponse/ContactForm/ContactForm.twig',
            [
                'contentModel' => new ContactFormContentModel(
                    content: $content,
                    fromUrl: (string) $this->requestFactory->make()->getUri(),
                    redirectUrl: (string) $linkModel->getUrl(),
                    formValues: $this->messageRetriever->retrieveFormValuesFromMessage(),
                ),
            ],
        );
    }
}
