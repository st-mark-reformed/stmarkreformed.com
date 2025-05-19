import { cache } from 'react';
import getRedisClient from '../../cache/RedisClient';
import { Entry } from './Entry';

export interface BaseInternalMediaReturnType {
    currentPage: number;
    perPage: number;
    totalResults: number;
    totalPages: number;
    pagesArray: Array<{
        isActive: boolean;
        label: string | number;
        target: string;
    }>;
    prevPageLink: string | null;
    nextPageLink: string | null;
    firstPageLink: string | null;
    lastPageLink: string | null;
    entries: Array<Entry>;
}

const GetPageData = cache(async (
    pageNum: number,
): Promise<null | BaseInternalMediaReturnType> => {
    const redis = getRedisClient();

    const redisPageData = await redis.get(
        `members:internal_media:page:${pageNum}`,
    );

    if (!redisPageData) {
        return null;
    }

    return JSON.parse(redisPageData);
});

export default GetPageData;
