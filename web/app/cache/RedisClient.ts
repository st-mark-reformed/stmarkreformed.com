import Redis from 'ioredis';
import { ConfigOptions, getConfigString } from '../ServerSideRunTimeConfig';

export default function getRedisClient () {
    if (!globalThis.redisClient) {
        const redis = new Redis({
            host: getConfigString(ConfigOptions.REDIS_HOST),
            lazyConnect: true,
            showFriendlyErrorStack: true,
            enableAutoPipelining: true,
            maxRetriesPerRequest: 0,
            retryStrategy: (times: number) => {
                if (times > 30) {
                    return undefined;
                }

                return 1000;
            },
        });

        redis.on('error', (error) => {
            // eslint-disable-next-line no-console
            console.warn('[Redis] Error', error);
        });

        globalThis.redisClient = redis;
    }

    return globalThis.redisClient;
}
