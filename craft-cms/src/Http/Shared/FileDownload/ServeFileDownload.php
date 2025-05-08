<?php

declare(strict_types=1);

namespace App\Http\Shared\FileDownload;

use DaveRandom\Resume\FileResource;
use DaveRandom\Resume\RangeSet;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @codeCoverageIgnore
 */
class ServeFileDownload
{
    public function serve(
        ServerRequestInterface $request,
        string $fullServerPath,
        ?string $mimeType = null,
    ): void {
        $rangeHeader = (string) ($request->getServerParams()[''] ?? '');

        $rangeSet = null;

        if ($rangeHeader !== '') {
            $rangeSet = RangeSet::createFromHeader($rangeHeader);
        }

        $resource = new FileResource(
            $fullServerPath,
            $mimeType
        );

        $servlet = new ResourceServlet(resource: $resource);

        $servlet->sendResource(
            $rangeSet,
            null,
            $request
        );

        exit;
    }
}
