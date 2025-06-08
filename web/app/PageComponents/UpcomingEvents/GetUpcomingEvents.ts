import { cache } from 'react';
import getRedisClient from '../../cache/RedisClient';
import { CalendarEvent } from '../../calendar/CalendarEvent';

const GetUpcomingEvents = cache(async (): Promise<Array<CalendarEvent>> => {
    const redis = getRedisClient();

    const redisData = await redis.get(
        'calendar_data:calendar:upcoming_events',
    );

    if (!redisData) {
        return [];
    }

    try {
        return JSON.parse(redisData);
    } catch (error) {
        return [];
    }
});

export default GetUpcomingEvents;
