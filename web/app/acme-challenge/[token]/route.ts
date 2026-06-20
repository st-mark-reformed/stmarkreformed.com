import { NextRequest, NextResponse } from 'next/server';
import fs from 'fs';

// certbot writes ACME HTTP-01 challenge files into this webroot, which is
// mounted from the host in docker-compose.prod.yml. Requests to
// /.well-known/acme-challenge/<token> are rewritten here (see next.config.js)
// so Let's Encrypt renewals keep working once the nginx proxy is removed.
const acmeChallengeDir = '/var/www/letsencrypt/.well-known/acme-challenge';

export async function GET (
    request: NextRequest,
    {
        params,
    }: {
        params: Promise<{
            token: string;
        }>;
    },
) {
    const { token } = await params;

    // The token is a single path segment written by certbot. Reject anything
    // that is not a plain token so a request cannot escape the challenge dir.
    if (!(/^[A-Za-z0-9_-]+$/).test(token)) {
        return new NextResponse('Not found', { status: 404 });
    }

    const filePath = [
        acmeChallengeDir,
        token,
    ].join('/');

    if (!fs.existsSync(filePath)) {
        return new NextResponse('Not found', { status: 404 });
    }

    try {
        const body = fs.readFileSync(filePath, 'utf8');

        return new NextResponse(body, {
            headers: {
                'Content-Type': 'text/plain',
                'Cache-Control': 'no-cache',
            },
        });
    } catch (error) {
        return new NextResponse('Not found', { status: 500 });
    }
}
