import React from 'react';
import { RequestFactory } from '../../../../api/request/RequestFactory';
import ApiResponseGate from '../../../ApiResponseGate';
import PageInnerClientSide from './PageInnerClientSide';
import { File } from './File';

export default async function PageInner () {
    const apiResponse = await RequestFactory().makeWithSignInRedirect({
        uri: '/cms/entries/messages/files',
        cacheSeconds: 0,
    });

    const files = apiResponse.json as unknown as Array<File>;

    return (
        <ApiResponseGate apiResponse={apiResponse}>
            <PageInnerClientSide files={files} />
        </ApiResponseGate>
    );
}
