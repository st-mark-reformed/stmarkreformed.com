import {
    RequestFactory as BaseRequestFactory,
    RefreshAccessTokenFactory,
    IoRedisRefreshLockFactory,
} from 'rxante-oauth';
import TokenRepositoryFactory from '../auth/TokenRepositoryFactory';
import getRedisClient from '../../cache/RedisClient';
import { ConfigOptions, getConfigString } from '../../ServerSideRunTimeConfig';

export default function RequestFactory (server: 'api' | 'auth' = 'api') {
    const tokenRepository = TokenRepositoryFactory();

    return BaseRequestFactory({
        appUrl: getConfigString(ConfigOptions.BASE_URL),
        requestBaseUrl: server === 'api'
            ? getConfigString(ConfigOptions.API_URL)
            : getConfigString(ConfigOptions.AUTH_URL),
        tokenRepository,
        refreshAccessToken: RefreshAccessTokenFactory({
            tokenRepository,
            refreshLock: IoRedisRefreshLockFactory({ redis: getRedisClient() }),
            wellKnownUrl: getConfigString(ConfigOptions.AUTH_WELL_KNOWN_URL),
            clientId: 'y8BwEVCNZpCJbWgit3LL6ctMaevF42dJ',
            clientSecret: getConfigString(ConfigOptions.AUTH_CLIENT_SECRET),
            redis: getRedisClient(),
        }),
        signInUri: '/api/auth/sign-in',
    });
}
