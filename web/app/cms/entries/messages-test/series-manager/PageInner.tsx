import React from 'react';
import { RequestFactory } from '../../../../api/request/RequestFactory';
import ApiResponseGate from '../../../ApiResponseGate';
import PageInnerClientSide from './PageInnerClientSide';

export default async function PageInner () {
    // Check if we have CMS access for now
    const apiResponse = await RequestFactory().makeWithSignInRedirect({
        uri: '/has-cms-access',
        cacheSeconds: 0,
    });

    return (
        <ApiResponseGate apiResponse={apiResponse}>
            <PageInnerClientSide />
        </ApiResponseGate>
    );
}
