<?php

declare(strict_types=1);

namespace App;

use App\Html\HtmlPath;
use BuzzingPixel\Templating\TemplateEngine;
use BuzzingPixel\Templating\TemplateEngineFactory as BPTemplateEngineFactory;
use Psr\Container\ContainerInterface;
use Slim\Csrf\Guard as CsrfGuard;

readonly class TemplateEngineFactory
{
    public function __construct(
        private CsrfGuard $csrfGuard,
        private ContainerInterface $di,
        private BPTemplateEngineFactory $bpTemplateEngineFactory,
    ) {
    }

    public function create(
        string|null $extends = HtmlPath::CENTERED_WRAPPER_LAYOUT,
    ): TemplateEngine {
        $engine = $this->bpTemplateEngineFactory->create();

        if ($extends !== null) {
            $engine->extends($extends);
        }

        $engine->addVar('di', $this->di);

        return $engine;
    }

    public function createWithCsrfTokens(
        string|null $extends = HtmlPath::CENTERED_WRAPPER_LAYOUT,
    ): TemplateEngine {
        $engine = $this->create($extends);

        $this->csrfGuard->generateToken();

        return $engine->addVar(
            'csrfTokenNameKey',
            $this->csrfGuard->getTokenNameKey(),
        )
            ->addVar(
                'csrfTokenName',
                $this->csrfGuard->getTokenName(),
            )
            ->addVar(
                'csrfTokenValueKey',
                $this->csrfGuard->getTokenValueKey(),
            )
            ->addVar(
                'csrfTokenValue',
                $this->csrfGuard->getTokenValue(),
            );
    }
}
