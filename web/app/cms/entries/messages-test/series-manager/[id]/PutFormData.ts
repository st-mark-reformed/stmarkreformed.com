'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { revalidatePath } from 'next/cache';
import { SeriesFormData } from '../EditSeries/SeriesFormData';
import { RequestFactory } from '../../../../../api/request/RequestFactory';
import { Result } from '../../../../../api/request/Result';

export default async function PutFormData (
    id: string,
    formData: SeriesFormData,
) {
    const response = await RequestFactory().makeWithToken({
        uri: `/cms/entries/messages/series-manager/${id}`,
        method: RequestMethods.PUT,
        payload: formData,
        cacheSeconds: 0,
    });

    revalidatePath('/cms/entries/messages-test/series-manager');

    return response.json as unknown as Result;
}
