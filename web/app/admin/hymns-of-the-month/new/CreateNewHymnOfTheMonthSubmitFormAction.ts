'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { redirect } from 'next/navigation';
import { revalidatePath } from 'next/cache';
import { CreateEditHymnOfTheMonthSubmitActionState } from '../CreateEditHymnOfTheMonthSubmitActionState';
import CreateEditHymnOfTheMonthParseFormData from '../CreateEditHymnOfTheMonthParseFormData';
import RequestFactory from '../../../api/request/RequestFactory';
import { ApiResponseJson } from '../../../api/request/ApiResponseJson';

export default async function CreateNewHymnOfTheMonthSubmitFormAction (
    prevState: CreateEditHymnOfTheMonthSubmitActionState,
    formData: FormData,
): Promise<CreateEditHymnOfTheMonthSubmitActionState> {
    const payload = CreateEditHymnOfTheMonthParseFormData(formData);

    const response = await RequestFactory().makeWithToken({
        uri: '/admin/hymns-of-the-month/new',
        method: RequestMethods.POST,
        cacheSeconds: 0,
        payload,
    });

    const responseJson = response.json as unknown as ApiResponseJson;

    if (responseJson.success) {
        revalidatePath('/admin/hymns-of-the-month');

        redirect('/admin/hymns-of-the-month');
    }

    return {
        ok: responseJson.success,
        success: responseJson.success,
        values: payload,
        errors: responseJson.errors || { error: 'An unknown error occurred' },
    };
}
