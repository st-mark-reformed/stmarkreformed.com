'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { NewProfileFormData } from './NewProfileFormData';
import { RequestFactory } from '../../../api/request/RequestFactory';
import { Result } from '../../../api/request/Result';

export default async function PostFormData (
    formData: NewProfileFormData,
): Promise<Result> {
    const response = await RequestFactory().makeWithToken({
        uri: '/cms/profiles',
        method: RequestMethods.POST,
        payload: formData,
        cacheSeconds: 0,
    });

    return response.json as unknown as Result;
}
