import React from 'react';
import { RequestFactory } from '../../../../api/request/RequestFactory';
import ApiResponseGate from '../../../ApiResponseGate';
import PageInnerClientSide from './PageInnerClientSide';
import { MessageSeries } from './MessageSeries';

export default async function PageInner () {
    const apiResponse = await RequestFactory().makeWithSignInRedirect({
        uri: '/cms/entries/messages/series',
        cacheSeconds: 0,
    });

    const messageSeries = apiResponse.json as unknown as Array<MessageSeries>;

    return (
        <ApiResponseGate apiResponse={apiResponse}>
            <PageInnerClientSide messageSeries={messageSeries} />
        </ApiResponseGate>
    );
}
