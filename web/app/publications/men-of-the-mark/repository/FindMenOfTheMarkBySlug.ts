import { cache } from 'react';
import getRedisClient from '../../../cache/RedisClient';
import { PublicationEntry } from '../../PublicationEntry';

const FindMenOfTheMarkBySlug = cache(async (slug: string): Promise<PublicationEntry | null> => {
    const redis = getRedisClient();

    const redisData = (await redis.get(
        `publications:men_of_the_mark:slug:${slug}`,
    )) as string;

    const entryData = JSON.parse(redisData) as { entry: PublicationEntry } | null;

    if (!entryData) {
        return null;
    }

    return entryData.entry;
});

export default FindMenOfTheMarkBySlug;
