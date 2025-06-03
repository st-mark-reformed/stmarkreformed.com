import { cache } from 'react';
import getRedisClient from '../../../cache/RedisClient';

const FindRecentSeries = cache(async (): Promise<Array<{
    title: string;
    slug: string;
}>> => {
    const redis = getRedisClient();

    const redisPageData = await redis.get('messages:most_recent_series');

    if (!redisPageData) {
        return [];
    }

    return JSON.parse(redisPageData);
});

export default FindRecentSeries;
