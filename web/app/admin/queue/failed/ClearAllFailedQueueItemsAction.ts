'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { revalidatePath } from 'next/cache';
import RequestFactory from '../../../api/request/RequestFactory';

export default async function ClearAllFailedQueueItemsAction (): Promise<void> {
    await RequestFactory().makeWithToken({
        uri: '/admin/queue/failed/clear-all',
        method: RequestMethods.POST,
        cacheSeconds: 0,
    });

    revalidatePath('/admin/queue/failed');
}
