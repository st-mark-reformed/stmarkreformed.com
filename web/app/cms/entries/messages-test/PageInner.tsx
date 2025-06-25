import React from 'react';
import { RequestFactory } from '../../../api/request/RequestFactory';
import ApiResponseGate from '../../ApiResponseGate';
import { Message } from './Message';
import PageInnerClientSide from './PageInnerClientSide';

export default async function PageInner () {
    const apiResponse = await RequestFactory().makeWithSignInRedirect({
        uri: '/cms/entries/messages',
        cacheSeconds: 0,
    });

    const allMessages = apiResponse.json as unknown as Array<Message>;

    const unpublishedMessages = allMessages.filter(
        (message) => !message.isPublished,
    );

    const publishedMessages = allMessages.filter(
        (message) => message.isPublished,
    );

    return (
        <ApiResponseGate apiResponse={apiResponse}>
            <PageInnerClientSide
                unpublishedMessages={unpublishedMessages}
                publishedMessages={publishedMessages}
            />
        </ApiResponseGate>
    );
}
