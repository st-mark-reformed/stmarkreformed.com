import { NextRequest } from 'next/server';
import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import RequestFactory from '../../../../../request/RequestFactory';

export async function PUT (request: NextRequest) {
    // /api/admin/uploads/{uploadId}/chunk/{index}
    const segments = request.nextUrl.pathname.split('/');
    const uploadId = segments[segments.length - 3] ?? '';
    const index = segments[segments.length - 1] ?? '';

    // The browser sends the raw chunk; base64 it for the JSON-only API hop.
    const arrayBuffer = await request.arrayBuffer();
    const data = Buffer.from(arrayBuffer).toString('base64');

    const response = await RequestFactory().makeWithToken({
        uri: `/admin/uploads/${encodeURIComponent(uploadId)}/chunk/${encodeURIComponent(index)}`,
        method: RequestMethods.PUT,
        cacheSeconds: 0,
        payload: { data },
    });

    return Response.json(response.json ?? {}, { status: response.status });
}
