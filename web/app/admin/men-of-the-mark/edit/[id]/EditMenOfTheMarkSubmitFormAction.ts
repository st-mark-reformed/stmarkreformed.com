'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { redirect } from 'next/navigation';
import { revalidatePath } from 'next/cache';
import { CreateEditMenOfTheMarkSubmitActionState } from '../../CreateEditMenOfTheMarkSubmitActionState';
import CreateEditMenOfTheMarkParseFormData from '../../CreateEditMenOfTheMarkParseFormData';
import RequestFactory from '../../../../api/request/RequestFactory';
import { ApiResponseJson } from '../../../../api/request/ApiResponseJson';

export default async function EditMenOfTheMarkSubmitFormAction (
    prevState: CreateEditMenOfTheMarkSubmitActionState,
    formData: FormData,
): Promise<CreateEditMenOfTheMarkSubmitActionState> {
    const payload = CreateEditMenOfTheMarkParseFormData(formData);

    const idValue = formData.get('id');
    const id = typeof idValue === 'string' ? idValue : '';

    const response = await RequestFactory().makeWithToken({
        uri: `/admin/men-of-the-mark/edit/${id}`,
        method: RequestMethods.PATCH,
        cacheSeconds: 0,
        payload,
    });

    const responseJson = response.json as unknown as ApiResponseJson;

    if (responseJson.success) {
        revalidatePath('/admin/men-of-the-mark');

        redirect('/admin/men-of-the-mark');
    }

    return {
        ok: responseJson.success,
        success: responseJson.success,
        values: payload,
        errors: responseJson.errors || { error: 'An unknown error occurred' },
    };
}
