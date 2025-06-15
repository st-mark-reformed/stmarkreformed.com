'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { revalidatePath } from 'next/cache';
import { ProfileFormData } from '../EditProfile/ProfileFormData';
import { RequestFactory } from '../../../api/request/RequestFactory';
import { Result } from '../../../api/request/Result';

export default async function PutFormData (
    id: string,
    formData: ProfileFormData,
) {
    const response = await RequestFactory().makeWithToken({
        uri: `/cms/profiles/${id}`,
        method: RequestMethods.PUT,
        payload: formData,
        cacheSeconds: 0,
    });

    revalidatePath('/cms/profiles');

    return response.json as unknown as Result;
}
