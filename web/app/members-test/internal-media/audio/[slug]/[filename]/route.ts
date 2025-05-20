import { NextRequest, NextResponse } from 'next/server';
import fs from 'fs';
import { TokenCookieIsValid } from '../../../../MemberTokenRepository';
import GetMimeType from '../../../../../audio/GetMimeType';

export async function GET (
    request: NextRequest,
    {
        params,
    }: {
        params: Promise<{
            slug: string;
            filename: string;
        }>;
    },
) {
    const tokenCookieIsValid = TokenCookieIsValid(
        request.cookies.get('member'),
    );

    if (!tokenCookieIsValid) {
        return NextResponse.json(
            {
                message: 'You cannot access this resource unless you are logged in',
            },
            { status: 401 },
        );
    }

    const paramsResolved = await params;

    const filePath = [
        '',
        'app',
        'filesAboveWebroot',
        'internal-audio',
        paramsResolved.slug,
        paramsResolved.filename,
    ].join('/');

    if (!fs.existsSync(filePath)) {
        return NextResponse.json(
            {
                message: 'File does not exist',
            },
            { status: 404 },
        );
    }

    try {
        const stats = fs.statSync(filePath);
        const fileSize = stats.size;
        const mimeType = GetMimeType(paramsResolved.filename);

        const range = request.headers.get('range');

        if (range) {
            const parts = range.replace(/bytes=/, '').split('-');
            const start = parseInt(parts[0], 10);
            const end = parts[1] ? parseInt(parts[1], 10) : fileSize - 1;

            if (Number.isNaN(start) || start < 0 || start >= fileSize || end >= fileSize) {
                return new Response(null, {
                    status: 416,
                    headers: {
                        'Content-Range': `bytes */${fileSize}`,
                    },
                });
            }

            const chunkSize = (end - start) + 1;

            // Read specific bytes from file
            const fileDescriptor = fs.openSync(filePath, 'r');
            const buffer = Buffer.alloc(chunkSize);
            fs.readSync(fileDescriptor, buffer, 0, chunkSize, start);
            fs.closeSync(fileDescriptor);

            return new Response(buffer, {
                status: 206,
                headers: {
                    'Content-Type': mimeType,
                    'Content-Length': chunkSize.toString(),
                    'Content-Range': `bytes ${start}-${end}/${fileSize}`,
                    'Accept-Ranges': 'bytes',
                    'Cache-Control': 'no-cache',
                },
            });
        }

        // For full file requests
        const buffer = fs.readFileSync(filePath);

        return new Response(buffer, {
            headers: {
                'Content-Type': mimeType,
                'Content-Length': fileSize.toString(),
                'Accept-Ranges': 'bytes',
                'Cache-Control': 'no-cache',
            },
        });
    } catch (error) {
        return NextResponse.json(
            {
                message: 'An unknown error has occurred',
            },
            { status: 500 },
        );
    }
}
