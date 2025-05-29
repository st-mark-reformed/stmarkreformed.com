import { cache } from 'react';
import getRedisClient from '../../../cache/RedisClient';
import { PublicationEntry } from '../../PublicationEntry';

interface ReturnType {
    entries: Array<PublicationEntry>;
}

const FindAllMenOfTheMarkEntries = cache(async (): Promise<ReturnType> => {
    const redis = getRedisClient();

    const redisPageData = await redis.get(
        'publications:men_of_the_mark:index',
    ) as string;

    return JSON.parse(redisPageData) ?? { entries: [] };
});

export default FindAllMenOfTheMarkEntries;
