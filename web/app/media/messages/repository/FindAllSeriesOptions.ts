import getRedisClient from '../../../cache/RedisClient';

export default async function FindAllSeriesOptions (): Promise<Record<string, string>> {
    const redis = getRedisClient();

    const redisData = await redis.get('messages:series_options');

    if (!redisData) {
        return {};
    }

    return JSON.parse(redisData);
}
