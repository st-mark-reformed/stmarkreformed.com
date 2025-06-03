import { cache } from 'react';
import getRedisClient from '../../../cache/RedisClient';
import { MessagesPageData } from '../../../audio/MessagesPageData';

const FindAllMessagesByPage = cache(async (
    pageNum: number,
): Promise<null | MessagesPageData> => {
    const redis = getRedisClient();

    const redisPageData = await redis.get(
        `messages:page:${pageNum}`,
    );

    if (!redisPageData) {
        return null;
    }

    return JSON.parse(redisPageData);
});

export default FindAllMessagesByPage;
