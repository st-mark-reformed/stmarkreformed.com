'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { revalidatePath } from 'next/cache';
import { RequestFactory } from '../../api/request/RequestFactory';
import { Result } from '../../api/request/Result';

export default async function DeleteProfiles (ids: Array<string>) {
    const response = await RequestFactory().makeWithToken({
        uri: '/cms/profiles',
        method: RequestMethods.DELETE,
        payload: { ids },
        cacheSeconds: 0,
    });

    revalidatePath('/cms/profiles');

    return response.json as unknown as Result;
}
