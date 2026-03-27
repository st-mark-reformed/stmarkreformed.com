'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { redirect } from 'next/navigation';
import { CreateEditSeriesSubmitActionState } from '../CreateEditSeriesSubmitActionState';
import CreateEditSeriesParseFormData from '../CreateEditSeriesParseFormData';
import RequestFactory from '../../../../api/request/RequestFactory';
import { ApiResponseJson } from '../../../../api/request/ApiResponseJson';

export default async function CreateNewSeriesSubmitFormAction (
    prevState: CreateEditSeriesSubmitActionState,
    formData: FormData,
): Promise<CreateEditSeriesSubmitActionState> {
    const {
        title,
        slug,
    } = CreateEditSeriesParseFormData(formData);

    const response = await RequestFactory().makeWithSignInRedirect({
        uri: '/admin/messages/series/new',
        method: RequestMethods.POST,
        cacheSeconds: 0,
        payload: {
            title,
            slug,
        },
    });

    const responseJson = response.json as unknown as ApiResponseJson;

    if (responseJson.success) {
        redirect('/admin/messages/series');
    }

    return {
        ok: responseJson.success,
        success: responseJson.success,
        values: {
            title,
            slug,
        },
        errors: responseJson.errors || { error: 'An unknown error occurred' },
    };
}
