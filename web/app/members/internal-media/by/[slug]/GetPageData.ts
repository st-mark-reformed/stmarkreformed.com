import { cache } from 'react';
import getRedisClient from '../../../../cache/RedisClient';
import { MessagesPageData } from '../../../../audio/MessagesPageData';

interface ByReturnType extends MessagesPageData {
    byName: string;
    bySlug: string;
}

const GetPageData = cache(async (
    slug: string,
    pageNum: number,
): Promise<null | ByReturnType> => {
    const redis = getRedisClient();

    const redisPageData = await redis.get(
        `members:internal_media:by:${slug}:${pageNum}`,
    );

    if (!redisPageData) {
        return null;
    }

    return JSON.parse(redisPageData);
});

export default GetPageData;
