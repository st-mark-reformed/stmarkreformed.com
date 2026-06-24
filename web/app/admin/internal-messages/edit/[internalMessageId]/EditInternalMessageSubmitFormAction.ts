'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { redirect } from 'next/navigation';
import { revalidatePath } from 'next/cache';
import { CreateEditInternalMessageSubmitActionState } from '../../CreateEditInternalMessageSubmitActionState';
import CreateEditInternalMessageParseFormData from '../../CreateEditInternalMessageParseFormData';
import RequestFactory from '../../../../api/request/RequestFactory';
import { ApiResponseJson } from '../../../../api/request/ApiResponseJson';

export default async function EditInternalMessageSubmitFormAction (
    prevState: CreateEditInternalMessageSubmitActionState,
    formData: FormData,
): Promise<CreateEditInternalMessageSubmitActionState> {
    // `audioPath` carries either an `upload:{id}` handle (a freshly uploaded
    // file) or the unchanged stored path; the chunked uploader already streamed
    // the bytes out of band.
    const payload = CreateEditInternalMessageParseFormData(formData);

    const internalMessageIdValue = formData.get('internalMessageId');
    const internalMessageId = typeof internalMessageIdValue === 'string'
        ? internalMessageIdValue
        : '';

    const response = await RequestFactory().makeWithToken({
        uri: `/admin/internal-messages/edit/${internalMessageId}`,
        method: RequestMethods.PATCH,
        cacheSeconds: 0,
        payload,
    });

    const responseJson = response.json as unknown as ApiResponseJson;

    if (responseJson.success) {
        revalidatePath('/admin/internal-messages');

        redirect('/admin/internal-messages');
    }

    return {
        ok: responseJson.success,
        success: responseJson.success,
        values: payload,
        errors: responseJson.errors || { error: 'An unknown error occurred' },
    };
}
