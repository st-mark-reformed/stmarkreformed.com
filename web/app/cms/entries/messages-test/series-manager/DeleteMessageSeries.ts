'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { revalidatePath } from 'next/cache';
import { RequestFactory } from '../../../../api/request/RequestFactory';
import { Result } from '../../../../api/request/Result';

export default async function DeleteMessageSeries (ids: Array<string>) {
    const response = await RequestFactory().makeWithToken({
        uri: '/cms/entries/messages/series',
        method: RequestMethods.DELETE,
        payload: { ids },
        cacheSeconds: 0,
    });

    revalidatePath('/cms/entries/messages-test/series-manager');

    return response.json as unknown as Result;
}
