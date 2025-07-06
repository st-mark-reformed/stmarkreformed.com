import React from 'react';
import { RequestFactory } from '../../../api/request/RequestFactory';
import ApiResponseGate from '../../ApiResponseGate';
import { Message } from './Message';
import PageInnerClientSide from './PageInnerClientSide';

export interface ApiJsonResponse {
    currentPage: number;
    perPage: number;
    totalResults: number;
    totalPages: number;
    pagesArray: Array<{
        isActive: boolean;
        label: string | number;
        target: string;
    }>;
    prevPageLink: string | null;
    nextPageLink: string | null;
    firstPageLink: string | null;
    lastPageLink: string | null;
    messages: Array<Message>;
}

export default async function MessageListingPage (
    {
        pageNum,
    }: {
        pageNum: number;
    },
) {
    const apiResponse = await RequestFactory().makeWithSignInRedirect({
        uri: `/cms/entries/messages/page/${pageNum}`,
        cacheSeconds: 0,
    });

    const pageData = apiResponse.json as unknown as ApiJsonResponse;

    let unpublishedMessages: Array<Message> = [];

    if (pageNum === 1) {
        const unpublishedRes = await RequestFactory().makeWithToken({
            uri: '/cms/entries/messages/unpublished',
            cacheSeconds: 0,
        });

        unpublishedMessages = unpublishedRes.json as unknown as Array<Message>;
    }

    return (
        <ApiResponseGate apiResponse={apiResponse}>
            <PageInnerClientSide
                unpublishedMessages={unpublishedMessages}
                pageData={pageData}
            />
        </ApiResponseGate>
    );
}
