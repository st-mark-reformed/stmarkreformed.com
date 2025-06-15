import React from 'react';
import { RequestFactory } from '../../api/request/RequestFactory';
import ApiResponseGate from '../ApiResponseGate';
import { Profile } from './Profile';
import PageInnerClientSide from './PageInnerClientSide';

export default async function PageInner () {
    const apiResponse = await RequestFactory().makeWithSignInRedirect({
        uri: '/cms/profiles',
        cacheSeconds: 0,
    });

    const profiles = apiResponse.json as unknown as Array<Profile>;

    return (
        <ApiResponseGate apiResponse={apiResponse}>
            <PageInnerClientSide profiles={profiles} />
        </ApiResponseGate>
    );
}
