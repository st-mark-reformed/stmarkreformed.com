'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { redirect } from 'next/navigation';
import { CreateEditProfileSubmitActionState } from '../../CreateEditProfileSubmitActionState';
import CreateEditProfileParseFormData from '../../CreateEditProfileParseFormData';
import RequestFactory from '../../../../api/request/RequestFactory';
import { ApiResponseJson } from '../../../../api/request/ApiResponseJson';

export default async function EditProfileSubmitFormAction (
    prevState: CreateEditProfileSubmitActionState,
    formData: FormData,
): Promise<CreateEditProfileSubmitActionState> {
    const payload = CreateEditProfileParseFormData(formData);

    const profileIdValue = formData.get('profileId');
    const profileId = typeof profileIdValue === 'string' ? profileIdValue : '';

    const response = await RequestFactory().makeWithToken({
        uri: `/admin/profiles/edit/${profileId}`,
        method: RequestMethods.PATCH,
        cacheSeconds: 0,
        payload,
    });

    const responseJson = response.json as unknown as ApiResponseJson;

    if (responseJson.success) {
        redirect('/admin/profiles');
    }

    return {
        ok: responseJson.success,
        success: responseJson.success,
        values: payload,
        errors: responseJson.errors || { error: 'An unknown error occurred' },
    };
}
