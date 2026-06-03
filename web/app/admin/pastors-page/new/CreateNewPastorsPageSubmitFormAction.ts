'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { redirect } from 'next/navigation';
import { revalidatePath } from 'next/cache';
import { CreateEditPastorsPageSubmitActionState } from '../CreateEditPastorsPageSubmitActionState';
import CreateEditPastorsPageParseFormData from '../CreateEditPastorsPageParseFormData';
import RequestFactory from '../../../api/request/RequestFactory';
import { ApiResponseJson } from '../../../api/request/ApiResponseJson';

export default async function CreateNewPastorsPageSubmitFormAction (
    prevState: CreateEditPastorsPageSubmitActionState,
    formData: FormData,
): Promise<CreateEditPastorsPageSubmitActionState> {
    const payload = CreateEditPastorsPageParseFormData(formData);

    const response = await RequestFactory().makeWithToken({
        uri: '/admin/pastors-page/new',
        method: RequestMethods.POST,
        cacheSeconds: 0,
        payload,
    });

    const responseJson = response.json as unknown as ApiResponseJson;

    if (responseJson.success) {
        revalidatePath('/admin/pastors-page');

        redirect('/admin/pastors-page');
    }

    return {
        ok: responseJson.success,
        success: responseJson.success,
        values: payload,
        errors: responseJson.errors || { error: 'An unknown error occurred' },
    };
}
