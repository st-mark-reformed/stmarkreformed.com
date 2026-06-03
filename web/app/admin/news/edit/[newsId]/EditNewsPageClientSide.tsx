'use client';

import React from 'react';
import { NewsItem } from '../../NewsItem';
import CreateEditNewsPage from '../../CreateEditNewsPage';

export default function EditNewsPageClientSide (
    {
        newsItem,
    }: {
        newsItem: NewsItem;
    },
) {
    return (
        <CreateEditNewsPage
            pageTitle={`Edit News: ${newsItem.title}`}
            submitFormAction="edit"
            initialValues={{
                ...newsItem,
                // datetime-local expects YYYY-MM-DDTHH:mm
                date: newsItem.date.replace(' ', 'T').slice(0, 16),
            }}
            newsId={newsItem.id}
        />
    );
}
