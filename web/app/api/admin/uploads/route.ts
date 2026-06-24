import { NextRequest } from 'next/server';
import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import RequestFactory from '../../request/RequestFactory';

export async function POST (request: NextRequest) {
    const payload = await request.json() as Record<string, unknown>;

    const response = await RequestFactory().makeWithToken({
        uri: '/admin/uploads',
        method: RequestMethods.POST,
        cacheSeconds: 0,
        payload,
    });

    return Response.json(response.json ?? {}, { status: response.status });
}
