import {
    IoRedisRefreshLockFactory,
    RefreshAccessTokenWithAuth0Factory,
    RequestFactory as BaseRequestFactory,
} from 'rxante-oauth';
import { TokenRepositoryFactory } from '../auth/TokenRepositoryFactory';
import getRedisClient from '../../cache/RedisClient';
import { ConfigOptions, getConfigString } from '../../ServerSideRunTimeConfig';

export function RequestFactory () {
    const tokenRepository = TokenRepositoryFactory();

    return BaseRequestFactory({
        appUrl: getConfigString(ConfigOptions.BASE_URL),
        requestBaseUrl: getConfigString(ConfigOptions.API_URL),
        tokenRepository,
        nextAuthProviderId: 'auth0',
        refreshAccessToken: RefreshAccessTokenWithAuth0Factory({
            tokenRepository,
            refreshLock: IoRedisRefreshLockFactory({ redis: getRedisClient() }),
            wellKnownUrl: getConfigString(ConfigOptions.NEXTAUTH_WELL_KNOWN_URL),
            clientId: getConfigString(ConfigOptions.NEXTAUTH_CLIENT_ID),
            clientSecret: getConfigString(ConfigOptions.NEXTAUTH_CLIENT_SECRET),
        }),
    });
}
