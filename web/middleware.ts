import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';
import { NextMiddlewareHeadersFactory } from 'rxante-oauth';

export async function middleware (req: NextRequest) {
    return NextResponse.next({
        request: { headers: NextMiddlewareHeadersFactory(req) },
    });
}
