import { TokenRepositoryForIoRedisFactory } from 'rxante-oauth';
import getRedisClient from '../../cache/RedisClient';
import { ConfigOptions, getConfigString } from '../../ServerSideRunTimeConfig';

export function TokenRepositoryFactory () {
    return TokenRepositoryForIoRedisFactory({
        redis: getRedisClient(),
        secret: getConfigString(ConfigOptions.NEXTAUTH_SECRET),
        redisTokenExpireTimeInSeconds: 864000, // 10 days
    });
}
