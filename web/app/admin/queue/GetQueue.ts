import RequestFactory from '../../api/request/RequestFactory';

interface QueueItems {
    key: string;
    handle: string;
    name: string;
    jobs: {
        class: string;
        method: string;
    }[];
}

export default async function GetQueue (): Promise<QueueItems[]> {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: '/admin/queue',
        cacheSeconds: 0,
    });

    return response.json as unknown as QueueItems[];
}
