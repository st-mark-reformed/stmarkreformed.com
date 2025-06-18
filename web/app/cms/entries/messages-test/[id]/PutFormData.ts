'use server';

import RequestMethods from 'rxante-oauth/dist/Request/RequestMethods';
import { revalidatePath } from 'next/cache';
import { MessageFormData } from '../EditMessage/MessageFormData';
import { Result } from '../../../../api/request/Result';
import { RequestFactory } from '../../../../api/request/RequestFactory';

export default async function PutFormData (
    id: string,
    formData: MessageFormData,
): Promise<Result> {
    const response = await RequestFactory().makeWithToken({
        uri: `/cms/entries/messages/${id}`,
        method: RequestMethods.PUT,
        payload: formData,
        cacheSeconds: 0,
    });

    revalidatePath('/cms/entries/messages-test');

    return response.json as unknown as Result;
}
