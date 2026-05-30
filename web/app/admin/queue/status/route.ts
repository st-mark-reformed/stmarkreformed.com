import { GetQueueStatus } from './GetQueueStatus';

export async function GET () {
    const { status, queueStatus } = await GetQueueStatus();

    return Response.json(queueStatus, { status });
}
