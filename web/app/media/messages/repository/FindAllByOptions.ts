import getRedisClient from '../../../cache/RedisClient';

export interface ByOptions {
    leadership: Record<string, string>;
    others: Record<string, string>;
}

export default async function FindAllByOptions (): Promise<ByOptions> {
    const redis = getRedisClient();

    const redisData = await redis.get('messages:by_options');

    if (!redisData) {
        return {
            leadership: {},
            others: {},
        };
    }

    return JSON.parse(redisData);
}
