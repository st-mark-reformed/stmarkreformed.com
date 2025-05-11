import getRedisClient from '../../cache/RedisClient';

export async function GET () {
    const redis = getRedisClient();

    let icsData = await redis.get('calendar_data:calendar:ics');

    if (typeof icsData !== 'string') {
        icsData = '';
    }

    return new Response(icsData, {
        headers: {
            'cache-control': 'must-revalidate, post-check=0, pre-check=0',
            'content-type': 'text/calendar; charset=utf-8',
            expires: '0',
            pragma: 'public',
        },
    });
}
