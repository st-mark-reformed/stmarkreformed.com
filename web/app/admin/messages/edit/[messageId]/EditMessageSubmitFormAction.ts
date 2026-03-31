'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { redirect } from 'next/navigation';
import { CreateEditMessageSubmitActionState } from '../../CreateEditMessageSubmitActionState';
import CreateEditMessageParseFormData from '../../CreateEditMessageParseFormData';
import RequestFactory from '../../../../api/request/RequestFactory';
import { ApiResponseJson } from '../../../../api/request/ApiResponseJson';
import { FileToBase64 } from '../../FileToBase64';

export default async function EditMessageSubmitFormAction (
    prevState: CreateEditMessageSubmitActionState,
    formData: FormData,
): Promise<CreateEditMessageSubmitActionState> {
    const payload = CreateEditMessageParseFormData(formData);

    const messageIdValue = formData.get('messageId');
    const messageId = typeof messageIdValue === 'string' ? messageIdValue : '';

    const audioValue = formData.get('audioPath');
    const audioFile = audioValue instanceof File && audioValue.size > 0
        ? audioValue
        : null;

    if (audioFile) {
        payload.audioPath = await FileToBase64(audioFile);
    }

    const response = await RequestFactory().makeWithToken({
        uri: `/admin/messages/edit/${messageId}`,
        method: RequestMethods.PATCH,
        cacheSeconds: 0,
        payload,
    });

    const responseJson = response.json as unknown as ApiResponseJson;

    if (responseJson.success) {
        redirect('/admin/messages');
    }

    return {
        ok: responseJson.success,
        success: responseJson.success,
        values: payload,
        errors: responseJson.errors || { error: 'An unknown error occurred' },
    };
}
