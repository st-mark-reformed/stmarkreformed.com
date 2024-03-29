<?php

declare(strict_types=1);

namespace App\Http\Shared\FileDownload;

use DaveRandom\Resume\DefaultOutputWriter;
use DaveRandom\Resume\OutputWriter;
use DaveRandom\Resume\Range;
use DaveRandom\Resume\RangeSet;
use DaveRandom\Resume\RangeUnitProvider;
use DaveRandom\Resume\Resource as ResumeResource;
use Psr\Http\Message\ServerRequestInterface;

use function implode;
use function mb_strtolower;
use function trim;

/**
 * Based on @see \DaveRandom\Resume\ResourceServlet
 * Unfortunately, That class does not set the right content-length header
 * when range(s) specified
 *
 * @codeCoverageIgnore
 */
class ResourceServlet
{
    private ResumeResource $resource;

    public function __construct(ResumeResource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Send data from a file based on the current Range header
     *
     * @param RangeSet|null     $rangeSet     Range header on which the transmission will be based
     * @param OutputWriter|null $outputWriter Output writer via which resource will be sent
     */
    public function sendResource(
        ?RangeSet $rangeSet = null,
        ?OutputWriter $outputWriter = null,
        ?ServerRequestInterface $request = null
    ): void {
        if ($outputWriter === null) {
            $outputWriter = new DefaultOutputWriter();
        }

        // Send the requested ranges
        $size = $this->resource->getLength();

        if ($rangeSet === null) {
            // No ranges requested, just send the whole file
            $outputWriter->setResponseCode(200);

            $this->sendHeaders($outputWriter);

            $outputWriter->sendHeader(
                'content-length',
                (string) $size,
            );

            $method = 'get';

            if ($request !== null) {
                $method = mb_strtolower($request->getMethod());
            }

            if ($method === 'head') {
                return;
            }

            $this->resource->sendData($outputWriter);

            return;
        }

        $ranges = $rangeSet->getRangesForSize($size);

        $outputWriter->setResponseCode(206);

        $this->sendHeaders($outputWriter);

        $outputWriter->sendHeader(
            'Content-Range',
            $this->getContentRangeHeader(
                $rangeSet->getUnit(),
                $ranges,
                $size
            ),
        );

        foreach ($ranges as $range) {
            $outputWriter->sendHeader(
                'content-length',
                (string) $range->getLength(),
            );

            if (
                $request !== null &&
                mb_strtolower($request->getMethod()) === 'head'
            ) {
                continue;
            }

            $this->resource->sendData(
                $outputWriter,
                $range
            );
        }
    }

    /**
     * Generate the default response headers for this resource
     *
     * @return string[]
     */
    private function generateDefaultHeaders(): array
    {
        $ranges = $this->resource instanceof RangeUnitProvider
            ? implode(',', $this->resource->getRangeUnits())
            : 'bytes';

        if ($ranges === '') {
            $ranges = 'none';
        }

        return [
            'content-type' => $this->resource->getMimeType(),
            'accept-ranges' => $ranges,
        ];
    }

    /**
     * Send the headers that are included regardless of whether a range
     * was requested
     */
    private function sendHeaders(OutputWriter $outputWriter): void
    {
        $headers = $this->generateDefaultHeaders();

        /** @psalm-suppress MixedAssignment */
        foreach ($this->resource->getAdditionalHeaders() as $name => $value) {
            $headers[mb_strtolower((string) $name)] = (string) $value;
        }

        /** @psalm-suppress MixedAssignment */
        foreach ($headers as $name => $value) {
            $outputWriter->sendHeader(
                trim((string) $name),
                trim($value)
            );
        }
    }

    /**
     * Create a Content-Range header corresponding to the specified unit
     * and ranges
     *
     * @param Range[] $ranges
     */
    private function getContentRangeHeader(
        string $unit,
        array $ranges,
        int $size
    ): string {
        return $unit . ' ' . implode(',', $ranges) . '/' . $size;
    }
}
