import React from 'react';
import GetMessages from './GetMessages';
import MessagesPageClientSide from './MessagesPageClientSide';

export default async function MessagesPage (
    {
        pageNum,
    }: {
        pageNum: number;
    },
) {
    const pageData = await GetMessages(pageNum);

    return (
        <MessagesPageClientSide
            messages={pageData.entries}
            currentPage={pageData.currentPage}
            totalPages={pageData.totalPages}
        />
    );
}
