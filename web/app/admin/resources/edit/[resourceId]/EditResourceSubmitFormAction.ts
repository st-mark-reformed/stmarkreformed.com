'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { redirect } from 'next/navigation';
import { revalidatePath } from 'next/cache';
import { CreateEditResourceSubmitActionState } from '../../CreateEditResourceSubmitActionState';
import CreateEditResourceParseFormData from '../../CreateEditResourceParseFormData';
import RequestFactory from '../../../../api/request/RequestFactory';
import { ApiResponseJson } from '../../../../api/request/ApiResponseJson';

export default async function EditResourceSubmitFormAction (
    prevState: CreateEditResourceSubmitActionState,
    formData: FormData,
): Promise<CreateEditResourceSubmitActionState> {
    const payload = CreateEditResourceParseFormData(formData);

    const resourceIdValue = formData.get('resourceId');
    const resourceId = typeof resourceIdValue === 'string' ? resourceIdValue : '';

    const response = await RequestFactory().makeWithToken({
        uri: `/admin/resources/edit/${resourceId}`,
        method: RequestMethods.PATCH,
        cacheSeconds: 0,
        payload,
    });

    const responseJson = response.json as unknown as ApiResponseJson;

    if (responseJson.success) {
        revalidatePath('/admin/resources');

        redirect('/admin/resources');
    }

    return {
        ok: responseJson.success,
        success: responseJson.success,
        values: payload,
        errors: responseJson.errors || { error: 'An unknown error occurred' },
    };
}
