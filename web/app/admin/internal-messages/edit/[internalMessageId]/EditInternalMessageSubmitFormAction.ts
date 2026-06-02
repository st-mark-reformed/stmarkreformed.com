'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { redirect } from 'next/navigation';
import { revalidatePath } from 'next/cache';
import { CreateEditInternalMessageSubmitActionState } from '../../CreateEditInternalMessageSubmitActionState';
import CreateEditInternalMessageParseFormData from '../../CreateEditInternalMessageParseFormData';
import RequestFactory from '../../../../api/request/RequestFactory';
import { ApiResponseJson } from '../../../../api/request/ApiResponseJson';
import { FileToBase64 } from '../../FileToBase64';

export default async function EditInternalMessageSubmitFormAction (
    prevState: CreateEditInternalMessageSubmitActionState,
    formData: FormData,
): Promise<CreateEditInternalMessageSubmitActionState> {
    const payload = CreateEditInternalMessageParseFormData(formData);

    const internalMessageIdValue = formData.get('internalMessageId');
    const internalMessageId = typeof internalMessageIdValue === 'string'
        ? internalMessageIdValue
        : '';

    const audioBase64Value = formData.get('audioPath');
    const audioBase64 = typeof audioBase64Value === 'string'
        ? audioBase64Value
        : '';

    const audioFileValue = formData.get('audioPathFile');
    const audioFile = audioFileValue instanceof File && audioFileValue.size > 0
        ? audioFileValue
        : null;

    if (audioBase64) {
        payload.audioPath = audioBase64;
    } else if (audioFile) {
        payload.audioPath = await FileToBase64(audioFile);
    }

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
