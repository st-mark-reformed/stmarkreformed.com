import { cache } from 'react';
import { HymnEntry } from '../HymnEntry';
import getRedisClient from '../../../cache/RedisClient';

interface ReturnType {
    entry: HymnEntry;
}

const GetPageData = cache(async (slug: string): Promise<null | ReturnType> => {
    const redis = getRedisClient();

    const redisPageData = await redis.get(
        `members:hymns_of_the_month:slug:${slug}`,
    );

    if (!redisPageData) {
        return null;
    }

    return JSON.parse(redisPageData);
});

export default GetPageData;
