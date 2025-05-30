import { cache } from 'react';
import getRedisClient from '../../cache/RedisClient';
import { NewsItemsReturn } from './NewsItemsReturn';

const FindNewsItemsByPage = cache(async (
    pageNum: number,
): Promise<null | NewsItemsReturn> => {
    const redis = getRedisClient();

    const redisPageData = await redis.get(
        `news:page:${pageNum}`,
    );

    if (!redisPageData) {
        return null;
    }

    return JSON.parse(redisPageData);
});

export default FindNewsItemsByPage;
