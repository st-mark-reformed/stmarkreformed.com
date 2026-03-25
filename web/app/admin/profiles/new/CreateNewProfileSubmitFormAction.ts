'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { redirect } from 'next/navigation';
import RequestFactory from '../../../api/request/RequestFactory';
import { CreateEditProfileSubmitActionState } from '../CreateEditProfileSubmitActionState';
import { ApiResponseJson } from '../../../api/request/ApiResponseJson';
import CreateEditProfileParseFormData from '../CreateEditProfileParseFormData';

export default async function CreateNewProfileSubmitFormAction (
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

    const response = await RequestFactory().makeWithToken({
        uri: '/admin/profiles/new',
        method: RequestMethods.POST,
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
