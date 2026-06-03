import React from 'react';
import GetNews from './GetNews';
import NewsPageClientSide from './NewsPageClientSide';

export default async function NewsPage (
    {
        pageNum,
        keyword,
    }: {
        pageNum: number;
        keyword: string;
    },
) {
    const pageData = await GetNews(pageNum, keyword);

    return (
        <NewsPageClientSide
            newsItems={pageData.entries}
            currentPage={pageData.currentPage}
            totalPages={pageData.totalPages}
            keyword={keyword}
        />
    );
}
