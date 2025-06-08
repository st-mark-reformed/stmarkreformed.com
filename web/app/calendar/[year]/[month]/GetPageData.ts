import { cache } from 'react';
import getRedisClient from '../../../cache/RedisClient';
import { CalendarPageParams } from './CalendarPageParams';
import { CalendarEvent } from '../../CalendarEvent';

interface Day {
    isInPast: boolean;
    isCurrentDay: boolean;
    isActiveMonth: boolean;
    ymd: string;
    year: number;
    month: number;
    day: number;
    events: Array<CalendarEvent>;
}

interface ReturnType {
    pagePath: string;
    monthDays: Array<Day>;
    monthRows: number;
    monthString: string;
    dateHeading: string;
    monthEventsList: Array<CalendarEvent>;
}

const GetPageData = cache(async (
    params: CalendarPageParams,
): Promise<null | ReturnType> => {
    const { year, month } = params;
    const key = [
        'calendar_data:calendar:page:calendar',
        year,
        month,
    ].join('/');

    const redis = getRedisClient();

    const redisPageData = await redis.get(key);

    if (!redisPageData) {
        return null;
    }

    return JSON.parse(redisPageData) as ReturnType;
});

export default GetPageData;
