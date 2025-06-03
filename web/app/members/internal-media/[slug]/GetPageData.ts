import { cache } from 'react';
import { Entry } from '../../../audio/Entry';
import getRedisClient from '../../../cache/RedisClient';

interface ReturnType {
    entry: Entry;
}

const GetPageData = cache(async (slug: string): Promise<null | ReturnType> => {
    const redis = getRedisClient();

    const redisPageData = await redis.get(
        `members:internal_media:slug:${slug}`,
    );

    if (!redisPageData) {
        return null;
    }

    return JSON.parse(redisPageData);
});

export default GetPageData;
