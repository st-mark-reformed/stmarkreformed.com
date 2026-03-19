import { TokenRepositoryForIoRedisFactory } from 'rxante-oauth';
import getRedisClient from '../../cache/RedisClient';

export default function TokenRepositoryFactory () {
    return TokenRepositoryForIoRedisFactory({
        redis: getRedisClient(),
        redisTokenExpireTimeInSeconds: 4800,
    });
}
