'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { revalidatePath } from 'next/cache';
import { RequestFactory } from '../../../../api/request/RequestFactory';
import { Result } from '../../../../api/request/Result';

export default async function DeleteFiles (names: Array<string>) {
    const response = await RequestFactory().makeWithToken({
        uri: '/cms/entries/messages/files',
        method: RequestMethods.DELETE,
        payload: { names },
        cacheSeconds: 0,
    });

    revalidatePath('/cms/entries/messages-test/file-manager');

    return response.json as unknown as Result;
}
