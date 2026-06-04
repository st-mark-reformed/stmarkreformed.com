import React from 'react';
import GetHymnsOfTheMonth from './GetHymnsOfTheMonth';
import HymnsOfTheMonthPageClientSide from './HymnsOfTheMonthPageClientSide';

export default async function HymnsOfTheMonthPage (
    {
        pageNum,
        keyword,
    }: {
        pageNum: number;
        keyword: string;
    },
) {
    const pageData = await GetHymnsOfTheMonth(pageNum, keyword);

    return (
        <HymnsOfTheMonthPageClientSide
            hymnOfTheMonthItems={pageData.entries}
            currentPage={pageData.currentPage}
            totalPages={pageData.totalPages}
            keyword={keyword}
        />
    );
}
