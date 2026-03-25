'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { redirect } from 'next/navigation';
import { Values } from './Values';
import ParseFormData from './ParseFormData';
import RequestFactory from '../../../../api/request/RequestFactory';

export type SubmitFormActionState =
    | {
        ok: true;
        success: boolean;
        values: Values;
    }
    | {
        ok: false;
        success: boolean;
        values: Values;
        errors: Record<string, string>;
    };

interface ApiResponseJson {
    success: boolean;
    errors: Record<string, string>;
}

export default async function SubmitFormAction (
    prevState: SubmitFormActionState,
    formData: FormData,
) {
    const {
        titleOrHonorific,
        email,
        firstName,
        lastName,
        leadershipPosition,
        bio,
    } = ParseFormData(formData);

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
        errors: responseJson.errors,
    };
}
