<?php

declare(strict_types=1);

namespace App\Templating\TwigControl;

use craft\web\twig\TemplateLoader as CraftTemplateLoaderAlias;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

class TwigControlTest extends TestCase
{
    private TwigControl $service;

    /** @var mixed[] */
    private array $twigCalls = [];

    /** @var MockObject&FilesystemLoader */
    private mixed $filesystemLoader;

    /** @var CraftTemplateLoaderAlias&MockObject */
    private mixed $craftTemplateLoader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->twigCalls = [];

        $twig = $this->createMock(Environment::class);

        $twig->method('setLoader')->willReturnCallback(
            function (LoaderInterface $loader): void {
                $this->twigCalls[] = [
                    'method' => 'setLoader',
                    'loader' => $loader,
                ];
            }
        );

        $this->filesystemLoader = $this->createMock(
            FilesystemLoader::class,
        );

        $this->craftTemplateLoader = $this->createMock(
            CraftTemplateLoaderAlias::class,
        );

        $this->service = new TwigControl(
            twig: $twig,
            filesystemLoader: $this->filesystemLoader,
            craftTemplateLoader: $this->craftTemplateLoader,
        );
    }

    public function testUseCraftTwigLoader(): void
    {
        $this->service->useCraftTwigLoader();

        self::assertSame(
            [
                [
                    'method' => 'setLoader',
                    'loader' => $this->craftTemplateLoader,
                ],
            ],
            $this->twigCalls,
        );
    }

    public function testUseCustomTwigLoader(): void
    {
        $this->service->useCustomTwigLoader();

        self::assertSame(
            [
                [
                    'method' => 'setLoader',
                    'loader' => $this->filesystemLoader,
                ],
            ],
            $this->twigCalls,
        );
    }
}
