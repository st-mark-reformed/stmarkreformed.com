import { NextRequest, NextResponse } from 'next/server';
import fs from 'fs';
import { TokenCookieIsValid } from '../../../MemberTokenRepository';
import GetMimeType from '../../../../audio/GetMimeType';

export async function GET (
    request: NextRequest,
    {
        params,
    }: {
        params: Promise<{
            filepath: Array<string>;
        }>;
    },
) {
    const tokenCookieIsValid = await TokenCookieIsValid(
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

    const fullFilePath = [
        '',
        'app',
        'filesAboveWebroot',
        ...paramsResolved.filepath,
    ].join('/');

    if (!fs.existsSync(fullFilePath)) {
        return NextResponse.json(
            {
                message: 'File does not exist',
            },
            { status: 404 },
        );
    }

    try {
        const stats = fs.statSync(fullFilePath);
        const fileSize = stats.size;
        const mimeType = GetMimeType(fullFilePath);

        const buffer = fs.readFileSync(fullFilePath);

        return new Response(buffer, {
            headers: {
                'Content-Type': mimeType,
                'Content-Length': fileSize.toString(),
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
