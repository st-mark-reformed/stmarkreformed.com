import React from 'react';
import GetInternalMessages from './GetInternalMessages';
import InternalMessagesPageClientSide from './InternalMessagesPageClientSide';

export default async function InternalMessagesPage (
    {
        pageNum,
    }: {
        pageNum: number;
    },
) {
    const pageData = await GetInternalMessages(pageNum);

    return (
        <InternalMessagesPageClientSide
            messages={pageData.entries}
            currentPage={pageData.currentPage}
            totalPages={pageData.totalPages}
        />
    );
}
