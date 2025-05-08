<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm;

use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\FormValues;
use Twig\Markup;

class ContactFormContentModel
{
    public function __construct(
        private Markup $content,
        private string $fromUrl,
        private string $redirectUrl,
        private ?FormValues $formValues,
    ) {
    }

    public function content(): Markup
    {
        return $this->content;
    }

    public function fromUrl(): string
    {
        return $this->fromUrl;
    }

    public function redirectUrl(): string
    {
        return $this->redirectUrl;
    }

    public function formValues(): ?FormValues
    {
        return $this->formValues;
    }
}
