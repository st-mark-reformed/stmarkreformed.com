<?php

declare(strict_types=1);

namespace App\Templating\TwigExtensions\ReadJson;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use function file_get_contents;
use function json_decode;

class ReadJson extends AbstractExtension
{
    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [$this->getFunction()];
    }

    private function getFunction(): TwigFunction
    {
        return new TwigFunction(
            'readJson',
            [$this, 'readJsonFunction']
        );
    }

    /**
     * @return mixed[]
     *
     * @psalm-suppress MixedInferredReturnType
     */
    public function readJsonFunction(string $filePath): array
    {
        /** @psalm-suppress MixedReturnStatement */
        return json_decode(
            (string) file_get_contents($filePath),
            true,
        );
    }
}
