import RequestFactory from '../../request/RequestFactory';

export async function GET () {
    try {
        await RequestFactory().makeWithToken({
            uri: '/keep-alive',
            cacheSeconds: 0,
        });
    } catch (error) { /* empty */ }

    return new Response('OK');
}
