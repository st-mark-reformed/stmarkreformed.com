import React from 'react';
import GetMenOfTheMark from './GetMenOfTheMark';
import MenOfTheMarkPageClientSide from './MenOfTheMarkPageClientSide';

export default async function MenOfTheMarkPage (
    {
        pageNum,
        keyword,
    }: {
        pageNum: number;
        keyword: string;
    },
) {
    const pageData = await GetMenOfTheMark(pageNum, keyword);

    return (
        <MenOfTheMarkPageClientSide
            items={pageData.entries}
            currentPage={pageData.currentPage}
            totalPages={pageData.totalPages}
            keyword={keyword}
        />
    );
}
