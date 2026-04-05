import RequestFactory from '../../api/request/RequestFactory';

interface Schedule {
    runEvery: string;
    class: string;
    method: string;
    lastRunStartAt: string;
    lastRunEndAt: string;
}

export default async function GetSchedule (): Promise<Schedule[]> {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: '/admin/schedule',
        cacheSeconds: 0,
    });

    return response.json as unknown as Schedule[];
}
