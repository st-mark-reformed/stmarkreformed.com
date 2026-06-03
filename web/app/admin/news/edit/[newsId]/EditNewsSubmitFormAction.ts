'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { redirect } from 'next/navigation';
import { revalidatePath } from 'next/cache';
import { CreateEditNewsSubmitActionState } from '../../CreateEditNewsSubmitActionState';
import CreateEditNewsParseFormData from '../../CreateEditNewsParseFormData';
import RequestFactory from '../../../../api/request/RequestFactory';
import { ApiResponseJson } from '../../../../api/request/ApiResponseJson';

export default async function EditNewsSubmitFormAction (
    prevState: CreateEditNewsSubmitActionState,
    formData: FormData,
): Promise<CreateEditNewsSubmitActionState> {
    const payload = CreateEditNewsParseFormData(formData);

    const newsIdValue = formData.get('newsId');
    const newsId = typeof newsIdValue === 'string' ? newsIdValue : '';

    const response = await RequestFactory().makeWithToken({
        uri: `/admin/news/edit/${newsId}`,
        method: RequestMethods.PATCH,
        cacheSeconds: 0,
        payload,
    });

    const responseJson = response.json as unknown as ApiResponseJson;

    if (responseJson.success) {
        revalidatePath('/admin/news');

        redirect('/admin/news');
    }

    return {
        ok: responseJson.success,
        success: responseJson.success,
        values: payload,
        errors: responseJson.errors || { error: 'An unknown error occurred' },
    };
}
