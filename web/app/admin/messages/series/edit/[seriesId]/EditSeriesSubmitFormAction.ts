'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { redirect } from 'next/navigation';
import { CreateEditSeriesSubmitActionState } from '../../CreateEditSeriesSubmitActionState';
import CreateEditSeriesParseFormData from '../../CreateEditSeriesParseFormData';
import RequestFactory from '../../../../../api/request/RequestFactory';
import { ApiResponseJson } from '../../../../../api/request/ApiResponseJson';

export default async function EditSeriesSubmitFormAction (
    prevState: CreateEditSeriesSubmitActionState,
    formData: FormData,
) {
    const {
        title,
        slug,
    } = CreateEditSeriesParseFormData(formData);

    const seriesIdValue = formData.get('seriesId');
    const seriesId = typeof seriesIdValue === 'string' ? seriesIdValue : '';

    const response = await RequestFactory().makeWithSignInRedirect({
        uri: `/admin/messages/series/edit/${seriesId}`,
        method: RequestMethods.PATCH,
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
