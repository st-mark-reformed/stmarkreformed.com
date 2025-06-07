import { cache } from 'react';
import getRedisClient from '../../../cache/RedisClient';
import { Entry } from '../../../audio/Entry';

interface ReturnType {
    entry: Entry;
}

const FindMessageBySlug = cache(async (
    slug: string,
): Promise<null | ReturnType> => {
    const redis = getRedisClient();

    const redisPageData = await redis.get(`messages:slug:${slug}`);

    if (!redisPageData) {
        return null;
    }

    return JSON.parse(redisPageData);
});

export default FindMessageBySlug;
