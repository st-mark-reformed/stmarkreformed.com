import { cache } from 'react';
import { MessagesPageData } from '../../../audio/MessagesPageData';
import getRedisClient from '../../../cache/RedisClient';

interface ByReturnType extends MessagesPageData {
    byName: string;
    bySlug: string;
}

const FindAllMessagesBySpeakerByPage = cache(async (
    slug: string,
    pageNum: number,
): Promise<null | ByReturnType> => {
    const redis = getRedisClient();

    const tmp = await redis.keys(
        'messages:by:*',
    );

    const redisPageData = await redis.get(
        `messages:by:${slug}:${pageNum}`,
    );

    if (!redisPageData) {
        return null;
    }

    return JSON.parse(redisPageData);
});

export default FindAllMessagesBySpeakerByPage;
