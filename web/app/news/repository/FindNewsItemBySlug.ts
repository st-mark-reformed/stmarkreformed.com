import { cache } from 'react';
import getRedisClient from '../../cache/RedisClient';
import { NewsItem } from './NewsItem';

const FindNewsItemBySlug = cache(async (
    sectionHandle: string,
    slug: string,
): Promise<null | NewsItem> => {
    const redis = getRedisClient();

    const redisPageData = await redis.get(
        `${sectionHandle}:slug:${slug}`,
    );

    if (!redisPageData) {
        return null;
    }

    const entryData = JSON.parse(redisPageData) as {
        entry: NewsItem;
    };

    return entryData.entry;
});

export default FindNewsItemBySlug;
