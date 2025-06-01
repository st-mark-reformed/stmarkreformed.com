import { cache } from 'react';
import getRedisClient from '../../cache/RedisClient';
import { ResourcesItemsReturn } from './ResourcesItemsReturn';

const FindResourceItemsByPage = cache(async (
    pageNum: number,
): Promise<null | ResourcesItemsReturn> => {
    const redis = getRedisClient();

    const redisPageData = await redis.get(`resources:page:${pageNum}`);

    if (!redisPageData) {
        return null;
    }

    return JSON.parse(redisPageData);
});

export default FindResourceItemsByPage;
