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
    const {
        titleOrHonorific,
        email,
        firstName,
        lastName,
        leadershipPosition,
        bio,
    } = CreateEditProfileParseFormData(formData);

    const profileIdValue = formData.get('profileId');
    const profileId = typeof profileIdValue === 'string' ? profileIdValue : '';

    const response = await RequestFactory().makeWithToken({
        uri: `/admin/profiles/edit/${profileId}`,
        method: RequestMethods.PATCH,
        cacheSeconds: 0,
        payload: {
            titleOrHonorific,
            email,
            firstName,
            lastName,
            leadershipPosition,
            bio,
        },
    });

    const responseJson = response.json as unknown as ApiResponseJson;

    if (responseJson.success) {
        redirect('/admin/profiles');
    }

    return {
        ok: responseJson.success,
        success: responseJson.success,
        values: {
            titleOrHonorific,
            email,
            firstName,
            lastName,
            leadershipPosition,
            bio,
        },
        errors: responseJson.errors || { error: 'An unknown error occurred' },
    };
}
