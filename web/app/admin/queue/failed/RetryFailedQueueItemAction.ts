'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { revalidatePath } from 'next/cache';
import RequestFactory from '../../../api/request/RequestFactory';

export default async function RetryFailedQueueItemAction (
    formData: FormData,
): Promise<void> {
    const key = formData.get('key') as string;

    await RequestFactory().makeWithToken({
        uri: '/admin/queue/failed/retry',
        method: RequestMethods.POST,
        cacheSeconds: 0,
        payload: { key },
    });

    revalidatePath('/admin/queue/failed');
}
