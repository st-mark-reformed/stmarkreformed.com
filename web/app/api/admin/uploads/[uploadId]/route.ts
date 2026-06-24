import { NextRequest } from 'next/server';
import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import RequestFactory from '../../../request/RequestFactory';

export async function GET (request: NextRequest) {
    // /api/admin/uploads/{uploadId}
    const segments = request.nextUrl.pathname.split('/');
    const uploadId = segments[segments.length - 1] ?? '';

    const response = await RequestFactory().makeWithToken({
        uri: `/admin/uploads/${encodeURIComponent(uploadId)}`,
        method: RequestMethods.GET,
        cacheSeconds: 0,
    });

    return Response.json(response.json ?? {}, { status: response.status });
}
