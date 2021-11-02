<?php

declare(strict_types=1);

namespace App\Templating\TwigControl;

use craft\web\View;
use Twig\Markup;

use function array_keys;
use function array_merge;
use function array_reverse;
use function explode;
use function implode;
use function mb_strpos;
use function ob_clean;
use function ob_get_clean;
use function ob_start;
use function parse_url;

use const PHP_EOL;

/**
 * @psalm-suppress MixedArgument
 * @psalm-suppress MixedArgumentTypeCoercion
 * @psalm-suppress MixedArrayOffset
 * @psalm-suppress MixedAssignment
 * @codeCoverageIgnore
 * Yii is making us do some dirty things and this is too complicated to test
 * or, at least, I don't want to spend the time writing the tests
 */
class ViewManager
{
    public function __construct(private View $view)
    {
    }

    private bool $hasRegisteredAssetBundles = false;

    /** @var string[] */
    private array $unregisteredCssFileNames = [];

    public function unRegisterCssByFileName(string $fileName): void
    {
        $this->unregisteredCssFileNames[] = $fileName;
    }

    public function renderQueuedCss(): Markup
    {
        if (! $this->hasRegisteredAssetBundles) {
            $this->registerAssetBundles();
        }

        $cssFiles = $this->view->cssFiles;

        foreach ($this->unregisteredCssFileNames as $fileName) {
            foreach (array_keys($cssFiles) as $key) {
                $parsed = parse_url($key);

                /** @phpstan-ignore-next-line */
                $path = $parsed['path'] ?? '';

                $tagFileName = array_reverse(
                    explode('/', $path)
                )[0];

                if ($tagFileName !== $fileName) {
                    continue;
                }

                unset($cssFiles[$key]);
            }
        }

        $tags = array_merge(
            $cssFiles,
            $this->view->css,
        );

        return new Markup(
            implode(
                PHP_EOL,
                $tags,
            ),
            'UTF-8',
        );
    }

    public function renderQueuedJs(): Markup
    {
        if (! $this->hasRegisteredAssetBundles) {
            $this->registerAssetBundles();
        }

        $tags = [];

        foreach ($this->view->jsFiles as $jsFiles) {
            foreach ($jsFiles as $key => $jsFileTag) {
                if (mb_strpos($jsFileTag, '<') !== 0) {
                    continue;
                }

                $tags[$key] = $jsFileTag;
            }
        }

        foreach ($this->view->js as $js) {
            foreach ($js as $key => $tag) {
                $tags[$key] = implode('', [
                    '<script type="text/javascript">',
                    $tag,
                    '</script>',
                ]);
            }
        }

        return new Markup(
            implode(
                PHP_EOL,
                $tags,
            ),
            'UTF-8',
        );
    }

    private function registerAssetBundles(): void
    {
        $previousOutput = ob_get_clean();

        ob_start();

        $this->view->endBody();

        ob_clean();

        ob_start();

        echo $previousOutput;

        $this->hasRegisteredAssetBundles = true;
    }
}
