import { cache } from 'react';
import { MessagesPageData } from '../../../audio/MessagesPageData';
import getRedisClient from '../../../cache/RedisClient';

interface SeriesReturnType extends MessagesPageData {
    seriesName: string;
    seriesSlug: string;
}

const FindAllMessagesBySeriesByPage = cache(async (
    slug: string,
    pageNum: number,
): Promise<null | SeriesReturnType> => {
    const redis = getRedisClient();

    const redisPageData = await redis.get(
        `messages:series:${slug}:${pageNum}`,
    );

    if (!redisPageData) {
        return null;
    }

    return JSON.parse(redisPageData);
});

export default FindAllMessagesBySeriesByPage;
