'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { revalidatePath } from 'next/cache';
import RequestFactory from '../../../api/request/RequestFactory';

export default async function ClearFailedQueueItemAction (
    formData: FormData,
): Promise<void> {
    const key = formData.get('key') as string;

    await RequestFactory().makeWithToken({
        uri: '/admin/queue/failed/clear',
        method: RequestMethods.POST,
        cacheSeconds: 0,
        payload: { key },
    });

    revalidatePath('/admin/queue/failed');
}
