import RequestFactory from '../../../api/request/RequestFactory';

export interface QueueStatus {
    enqueued: number;
    failed: number;
}

export async function GetQueueStatus () {
    const response = await RequestFactory().makeWithToken({
        uri: '/admin/queue/status',
        cacheSeconds: 0,
    });

    return response.json as unknown as QueueStatus;
}

export async function GET () {
    return Response.json(await GetQueueStatus());
}
