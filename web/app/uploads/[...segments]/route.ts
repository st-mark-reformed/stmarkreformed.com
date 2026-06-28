import { NextRequest } from 'next/server';
import { createReadStream } from 'node:fs';
import { stat } from 'node:fs/promises';
import { Readable } from 'node:stream';
import path from 'node:path';

// Serve files from the uploads volume at request time. `next start` only serves
// public/ files that existed when the server booted, so user-uploaded files added
// to the volume afterward 404 until a restart. Streaming them from disk here makes
// new uploads available immediately. Next's static serving still takes precedence
// for files present at boot, so this only handles the ones it would otherwise miss.

export const runtime = 'nodejs';
export const dynamic = 'force-dynamic';

const UPLOADS_ROOT = path.join(process.cwd(), 'public', 'uploads');

const MIME_TYPES: Record<string, string> = {
    '.mp3': 'audio/mpeg',
    '.m4a': 'audio/mp4',
    '.wav': 'audio/wav',
    '.mp4': 'video/mp4',
    '.pdf': 'application/pdf',
    '.png': 'image/png',
    '.jpg': 'image/jpeg',
    '.jpeg': 'image/jpeg',
    '.gif': 'image/gif',
    '.webp': 'image/webp',
    '.svg': 'image/svg+xml',
    '.zip': 'application/zip',
    '.txt': 'text/plain',
};

function nodeToWeb (stream: Readable): ReadableStream<Uint8Array> {
    return Readable.toWeb(stream) as unknown as ReadableStream<Uint8Array>;
}

export async function GET (
    request: NextRequest,
    { params }: { params: Promise<{ segments: Array<string> }> },
) {
    const { segments } = await params;

    const resolved = path.resolve(UPLOADS_ROOT, ...segments);

    // Guard against path traversal: the resolved path must stay within uploads.
    if (resolved !== UPLOADS_ROOT && !resolved.startsWith(UPLOADS_ROOT + path.sep)) {
        return new Response('Not found', { status: 404 });
    }

    let fileStat;

    try {
        fileStat = await stat(resolved);
    } catch {
        return new Response('Not found', { status: 404 });
    }

    if (!fileStat.isFile()) {
        return new Response('Not found', { status: 404 });
    }

    const fileSize = fileStat.size;
    const contentType = MIME_TYPES[path.extname(resolved).toLowerCase()]
        ?? 'application/octet-stream';
    const etag = `"${fileSize.toString(16)}-${Math.floor(fileStat.mtimeMs).toString(16)}"`;
    const cacheControl = 'public, max-age=0, must-revalidate';

    const rangeHeader = request.headers.get('range');
    const rangeMatch = rangeHeader
        ? /^bytes=(\d*)-(\d*)$/.exec(rangeHeader)
        : null;

    if (!rangeMatch && request.headers.get('if-none-match') === etag) {
        return new Response(null, {
            status: 304,
            headers: { ETag: etag, 'Cache-Control': cacheControl },
        });
    }

    if (rangeMatch) {
        const start = rangeMatch[1] === '' ? 0 : Number(rangeMatch[1]);
        const end = rangeMatch[2] === '' ? fileSize - 1 : Number(rangeMatch[2]);

        if (
            Number.isNaN(start) || Number.isNaN(end)
            || start > end || end >= fileSize
        ) {
            return new Response('Range Not Satisfiable', {
                status: 416,
                headers: { 'Content-Range': `bytes */${fileSize}` },
            });
        }

        return new Response(
            nodeToWeb(createReadStream(resolved, { start, end })),
            {
                status: 206,
                headers: {
                    'Content-Type': contentType,
                    'Content-Length': String(end - start + 1),
                    'Content-Range': `bytes ${start}-${end}/${fileSize}`,
                    'Accept-Ranges': 'bytes',
                    'Cache-Control': cacheControl,
                    ETag: etag,
                    'Last-Modified': fileStat.mtime.toUTCString(),
                },
            },
        );
    }

    return new Response(nodeToWeb(createReadStream(resolved)), {
        status: 200,
        headers: {
            'Content-Type': contentType,
            'Content-Length': String(fileSize),
            'Accept-Ranges': 'bytes',
            'Cache-Control': cacheControl,
            ETag: etag,
            'Last-Modified': fileStat.mtime.toUTCString(),
        },
    });
}
