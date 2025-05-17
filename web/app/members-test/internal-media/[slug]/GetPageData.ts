import { Entry } from '../Entry';
import getRedisClient from '../../../cache/RedisClient';

interface ReturnType {
    entry: Entry;
}

export default async function GetPageData (
    slug: string,
): Promise<null | ReturnType> {
    const redis = getRedisClient();

    const redisPageData = await redis.get(
        `members:internal_media:slug:${slug}`,
    );

    if (!redisPageData) {
        return null;
    }

    return JSON.parse(redisPageData);
}
