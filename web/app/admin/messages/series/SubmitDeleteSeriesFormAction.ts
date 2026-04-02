'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { revalidatePath } from 'next/cache';
import { SubmitDeleteSeriesActionState } from './SubmitDeleteSeriesActionState';
import RequestFactory from '../../../api/request/RequestFactory';

interface ApiResponseJson {
    success: boolean;
    errors: [string];
}

export default async function SubmitDeleteSeriesFormAction (
    prevState: SubmitDeleteSeriesActionState,
    formData: FormData,
): Promise<SubmitDeleteSeriesActionState> {
    const items = formData.getAll('items[]');

    const response = await RequestFactory().makeWithToken({
        uri: '/admin/messages/series',
        method: RequestMethods.DELETE,
        cacheSeconds: 0,
        payload: { items },
    });

    const responseJson = response.json as unknown as ApiResponseJson;

    revalidatePath('/admin/messages/series');

    return {
        status: (() => {
            if (responseJson.success) {
                return 'success';
            }

            return 'failure';
        })(),
        iteration: prevState.iteration + 1,
        message: (() => {
            if (responseJson?.errors?.length > 0) {
                return responseJson.errors.join(', ');
            }

            return 'An unknown error occurred.';
        })(),
    };
}
