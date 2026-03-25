import RequestFactory from '../../request/RequestFactory';

export async function GET () {
    await RequestFactory().makeWithToken({
        uri: '/keep-alive',
        cacheSeconds: 0,
    });

    return new Response('OK');
}
