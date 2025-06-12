import React from 'react';
import { RequestFactory } from '../../../api/request/RequestFactory';
import EmptyState from '../../layout/EmptyState';
import ApiResponseGate from '../../ApiResponseGate';

export default async function PageInner () {
    // For now, just make sure we're logged in
    const apiResponse = await RequestFactory().makeWithSignInRedirect({
        uri: '/userinfo',
        cacheSeconds: 0,
    });

    return (
        <ApiResponseGate apiResponse={apiResponse}>
            <EmptyState
                itemNameSingular="Entry"
                itemNamePlural="Entries"
                buttonHref="/cms/entries/messages-test/new-entry"
            />
        </ApiResponseGate>
    );
}
