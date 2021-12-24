<?php

declare(strict_types=1);

namespace App\Http\Response\Pages\RenderPage;

interface RenderPageContract
{
    public function render(): string;
}
