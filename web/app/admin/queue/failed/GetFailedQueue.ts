import RequestFactory from '../../../api/request/RequestFactory';

export interface FailedQueueJob {
    class: string;
    method: string;
}

export interface FailedQueueItem {
    key: string;
    handle: string;
    name: string;
    jobs: FailedQueueJob[];
}

export interface FailedQueueEntry {
    key: string;
    message: string;
    code: number;
    file: string;
    line: number;
    trace: string;
    queueItem: FailedQueueItem;
    retried: boolean;
    date: string;
}

export default async function GetFailedQueue (): Promise<FailedQueueEntry[]> {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: '/admin/queue/failed',
        cacheSeconds: 0,
    });

    return response.json as unknown as FailedQueueEntry[];
}
