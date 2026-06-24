import { NextRequest } from 'next/server';
import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import RequestFactory from '../../../../request/RequestFactory';

export async function POST (request: NextRequest) {
    // /api/admin/uploads/{uploadId}/complete
    const segments = request.nextUrl.pathname.split('/');
    const uploadId = segments[segments.length - 2] ?? '';

    const response = await RequestFactory().makeWithToken({
        uri: `/admin/uploads/${encodeURIComponent(uploadId)}/complete`,
        method: RequestMethods.POST,
        cacheSeconds: 0,
        payload: {},
    });

    return Response.json(response.json ?? {}, { status: response.status });
}
