import { cache } from 'react';
import { BaseInternalMediaReturnType } from '../../GetPageData';
import getRedisClient from '../../../../cache/RedisClient';

interface SeriesReturnType extends BaseInternalMediaReturnType {
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
