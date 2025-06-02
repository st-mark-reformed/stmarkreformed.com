import { cache } from 'react';
import getRedisClient from '../../cache/RedisClient';
import { ResourceItem } from './ResourceItem';

const FindResourceItemBySlug = cache(async (
    slug: string,
): Promise<null | ResourceItem> => {
    const redis = getRedisClient();

    const redisPageData = await redis.get(`resources:slug:${slug}`);

    if (!redisPageData) {
        return null;
    }

    const entryData = JSON.parse(redisPageData) as {
        entry: ResourceItem;
    };

    return entryData.entry;
});

export default FindResourceItemBySlug;
