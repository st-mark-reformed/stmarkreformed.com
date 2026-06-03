import React from 'react';
import GetEditNews from './GetEditNews';
import EditNewsPageClientSide from './EditNewsPageClientSide';

export default async function EditNewsPage (
    {
        newsId,
    }: {
        newsId: string;
    },
) {
    const newsItem = await GetEditNews(newsId);

    if (!newsItem) {
        return null;
    }

    return <EditNewsPageClientSide newsItem={newsItem} />;
}
