import RequestFactory from '../../../api/request/RequestFactory';

export interface QueueStatus {
    enqueued: number;
    failed: number;
}

export interface QueueStatusResult {
    status: number;
    queueStatus: QueueStatus;
}

export async function GetQueueStatus (): Promise<QueueStatusResult> {
    const response = await RequestFactory().makeWithToken({
        uri: '/admin/queue/status',
        cacheSeconds: 0,
    });

    return {
        status: response.status,
        queueStatus: response.json as unknown as QueueStatus,
    };
}
