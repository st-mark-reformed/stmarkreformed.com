import RequestFactory from '../../request/RequestFactory';
import TokenRepositoryFactory from '../TokenRepositoryFactory';

export async function GET () {
    try {
        const response = await RequestFactory().makeWithToken({
            uri: '/keep-alive',
            cacheSeconds: 0,
        });

        if (response.status === 401) {
            return new Response('Unauthorized', { status: 401 });
        }

        /**
         * Extend the Redis TTL so the session stays alive while the page is
         * open. `makeWithToken` only writes to Redis when a refresh actually
         * fires (i.e. the access token was rejected); without this re-save the
         * Redis entry just ages out after `redisTokenExpireTimeInSeconds` even
         * though the page has been actively pinging.
         */
        const tokenRepository = TokenRepositoryFactory();

        const token = await tokenRepository.findTokenFromCookies();

        if (token) {
            await tokenRepository.setTokenBasedOnCookies(token);
        }

        return new Response('OK');
    } catch {
        return new Response('Service Unavailable', { status: 503 });
    }
}
