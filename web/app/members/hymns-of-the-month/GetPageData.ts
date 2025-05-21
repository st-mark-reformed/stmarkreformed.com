import { cache } from 'react';
import getRedisClient from '../../cache/RedisClient';
import { HymnEntry } from './HymnEntry';

interface ReturnType {
    entries: Array<HymnEntry>;
}

const GetPageData = cache(async (): Promise<ReturnType> => {
    const redis = getRedisClient();

    const redisPageData = await redis.get(
        'members:hymns_of_the_month:index',
    ) as string;

    return JSON.parse(redisPageData);
});

export default GetPageData;
