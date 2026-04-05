import { GetQueueStatus } from './GetQueueStatus';

export async function GET () {
    return Response.json(await GetQueueStatus());
}
