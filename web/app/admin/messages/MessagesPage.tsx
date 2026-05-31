import React from 'react';
import GetMessages from './GetMessages';
import MessagesPageClientSide from './MessagesPageClientSide';

export default async function MessagesPage (
    {
        pageNum,
        keyword,
    }: {
        pageNum: number;
        keyword: string;
    },
) {
    const pageData = await GetMessages(pageNum, keyword);

    return (
        <MessagesPageClientSide
            messages={pageData.entries}
            currentPage={pageData.currentPage}
            totalPages={pageData.totalPages}
            keyword={keyword}
        />
    );
}
