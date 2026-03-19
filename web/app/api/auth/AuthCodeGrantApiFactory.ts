import { WellKnownAuthCodeGrantApiFactory } from 'rxante-oauth';
import TokenRepositoryFactory from './TokenRepositoryFactory';
import { ConfigOptions, getConfigString } from '../../ServerSideRunTimeConfig';
import getRedisClient from '../../cache/RedisClient';

export async function AuthCodeGrantApiFactory () {
    return WellKnownAuthCodeGrantApiFactory({
        tokenRepository: TokenRepositoryFactory(),
        appUrl: getConfigString(ConfigOptions.BASE_URL),
        wellKnownUrl: getConfigString(ConfigOptions.AUTH_WELL_KNOWN_URL),
        clientId: 'y8BwEVCNZpCJbWgit3LL6ctMaevF42dJ',
        clientSecret: getConfigString(ConfigOptions.AUTH_CLIENT_SECRET),
        callbackUri: '/api/auth/callback',
        redis: getRedisClient(),
    });
}
