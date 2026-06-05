import React from 'react';
import GetMailingLists from './GetMailingLists';
import MailingListsPageClientSide from './MailingListsPageClientSide';

export default async function MailingListsPage (
    {
        pageNum,
        keyword,
    }: {
        pageNum: number;
        keyword: string;
    },
) {
    const pageData = await GetMailingLists(pageNum, keyword);

    return (
        <MailingListsPageClientSide
            mailingLists={pageData.entries}
            currentPage={pageData.currentPage}
            totalPages={pageData.totalPages}
            keyword={keyword}
        />
    );
}
