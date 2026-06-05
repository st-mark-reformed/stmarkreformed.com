'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { redirect } from 'next/navigation';
import { revalidatePath } from 'next/cache';
import { CreateEditMailingListSubmitActionState } from '../../CreateEditMailingListSubmitActionState';
import CreateEditMailingListParseFormData from '../../CreateEditMailingListParseFormData';
import RequestFactory from '../../../../api/request/RequestFactory';
import { ApiResponseJson } from '../../../../api/request/ApiResponseJson';

export default async function EditMailingListSubmitFormAction (
    prevState: CreateEditMailingListSubmitActionState,
    formData: FormData,
): Promise<CreateEditMailingListSubmitActionState> {
    const payload = CreateEditMailingListParseFormData(formData);

    const mailingListIdValue = formData.get('mailingListId');
    const mailingListId = typeof mailingListIdValue === 'string'
        ? mailingListIdValue
        : '';

    const response = await RequestFactory().makeWithToken({
        uri: `/admin/mailing-lists/edit/${mailingListId}`,
        method: RequestMethods.PATCH,
        cacheSeconds: 0,
        payload,
    });

    const responseJson = response.json as unknown as ApiResponseJson;

    if (responseJson.success) {
        revalidatePath('/admin/mailing-lists');

        redirect('/admin/mailing-lists');
    }

    return {
        ok: responseJson.success,
        success: responseJson.success,
        values: payload,
        errors: responseJson.errors || { error: 'An unknown error occurred' },
    };
}
