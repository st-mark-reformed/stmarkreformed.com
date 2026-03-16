<?php

declare(strict_types=1);

namespace App\LogIn;

use App\Html\ButtonConfig;
use App\Html\Glyphs\Glyph;
use App\Html\HtmlFormInputConfig;
use App\Html\HtmlFormInputType;
use App\Html\HtmlPath;
use App\TemplateEngineFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

readonly class GetLogInActionHandler
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private TemplateEngineFactory $templateEngineFactory,
    ) {
    }

    public function renderAndCreateResponse(
        string $redirectUrl,
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse(200);

        $response->getBody()->write(
            $this->templateEngineFactory->createWithCsrfTokens()
                ->templatePath(HtmlPath::HTML_FORM_LAYOUT)
                ->addVar(
                    'pageTitle',
                    'Log In to SMRC',
                )
                ->addVar('formAction', '/log-in')
                ->addVar('inputs', [
                    new HtmlFormInputConfig(
                        label: 'Redirect URL',
                        name: 'redirect_url',
                        value: $redirectUrl,
                        type: HtmlFormInputType::hidden,
                    ),
                    new HtmlFormInputConfig(
                        label: 'Email address',
                        name: 'email',
                        required: true,
                        type: HtmlFormInputType::email,
                    ),
                    new HtmlFormInputConfig(
                        label: 'Password',
                        name: 'password',
                        required: true,
                        type: HtmlFormInputType::password,
                    ),
                ])
                ->addVar('submitButtonConfig', new ButtonConfig(
                    content: 'Log In',
                    glyph: Glyph::ArrowRight,
                ))
                ->render(),
        );

        return $response;
    }
}
