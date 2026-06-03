import React from 'react';
import GetPastorsPage from './GetPastorsPage';
import PastorsPagePageClientSide from './PastorsPagePageClientSide';

export default async function PastorsPagePage (
    {
        pageNum,
        keyword,
    }: {
        pageNum: number;
        keyword: string;
    },
) {
    const pageData = await GetPastorsPage(pageNum, keyword);

    return (
        <PastorsPagePageClientSide
            pastorsPageItems={pageData.entries}
            currentPage={pageData.currentPage}
            totalPages={pageData.totalPages}
            keyword={keyword}
        />
    );
}
