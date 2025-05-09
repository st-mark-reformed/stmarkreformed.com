import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';

export async function middleware (req: NextRequest) {
    if (req.url.includes('_next')) {
        return NextResponse.next();
    }

    const headers = new Headers(req.headers);

    headers.set('middleware-pathname', req.nextUrl.pathname);

    headers.set('middleware-search-params', req.nextUrl.searchParams.toString());

    return NextResponse.next({
        request: {
            headers,
        },
    });
}
