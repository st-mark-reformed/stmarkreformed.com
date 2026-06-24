'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { redirect } from 'next/navigation';
import { revalidatePath } from 'next/cache';
import { CreateEditMessageSubmitActionState } from '../CreateEditMessageSubmitActionState';
import CreateEditMessageParseFormData from '../CreateEditMessageParseFormData';
import RequestFactory from '../../../api/request/RequestFactory';
import { ApiResponseJson } from '../../../api/request/ApiResponseJson';

export default async function CreateNewMessageSubmitFormAction (
    prevState: CreateEditMessageSubmitActionState,
    formData: FormData,
): Promise<CreateEditMessageSubmitActionState> {
    // `audioPath` carries either an `upload:{id}` handle (a freshly uploaded
    // file) or an unchanged stored path; the chunked uploader already streamed
    // the bytes out of band.
    const payload = CreateEditMessageParseFormData(formData);

    const response = await RequestFactory().makeWithToken({
        uri: '/admin/messages/new',
        method: RequestMethods.POST,
        cacheSeconds: 0,
        payload,
    });

    const responseJson = response.json as unknown as ApiResponseJson;

    if (responseJson.success) {
        revalidatePath('/admin/messages');

        redirect('/admin/messages');
    }

    return {
        ok: responseJson.success,
        success: responseJson.success,
        values: payload,
        errors: responseJson.errors || { error: 'An unknown error occurred' },
    };
}
