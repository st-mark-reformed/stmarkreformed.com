'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { revalidatePath } from 'next/cache';
import { SeriesFormData } from '../EditSeries/SeriesFormData';
import { Result } from '../../../../../api/request/Result';
import { RequestFactory } from '../../../../../api/request/RequestFactory';

export default async function PostFormData (
    formData: SeriesFormData,
): Promise<Result> {
    const response = await RequestFactory().makeWithToken({
        uri: '/cms/entries/messages/series-manager',
        method: RequestMethods.POST,
        payload: formData,
        cacheSeconds: 0,
    });

    revalidatePath('/cms/entries/messages-test/series-manager');

    return response.json as unknown as Result;
}
