import { cache } from 'react';
import getRedisClient from '../../../../cache/RedisClient';
import { MessagesPageData } from '../../../../audio/MessagesPageData';

interface SeriesReturnType extends MessagesPageData {
    seriesName: string;
    seriesSlug: string;
}

const GetPageData = cache(async (
    slug: string,
    pageNum: number,
):Promise<null | SeriesReturnType> => {
    const redis = getRedisClient();

    const redisPageData = await redis.get(
        `members:internal_media:series:${slug}:${pageNum}`,
    );

    if (!redisPageData) {
        return null;
    }

    return JSON.parse(redisPageData);
});

export default GetPageData;
