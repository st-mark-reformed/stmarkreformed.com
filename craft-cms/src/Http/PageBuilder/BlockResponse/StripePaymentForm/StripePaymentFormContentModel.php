<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\StripePaymentForm;

use Twig\Markup;

use function array_map;

class StripePaymentFormContentModel
{
    /** @var Markup[] */
    private array $forms = [];

    /**
     * @param Markup[] $forms
     */
    public function __construct(
        private bool $noTopSpace,
        array $forms,
    ) {
        array_map(
            [$this, 'addMarkup'],
            $forms,
        );
    }

    private function addMarkup(Markup $markup): void
    {
        $this->forms[] = $markup;
    }

    public function noTopSpace(): bool
    {
        return $this->noTopSpace;
    }

    /**
     * @return Markup[]
     */
    public function forms(): array
    {
        return $this->forms;
    }
}
