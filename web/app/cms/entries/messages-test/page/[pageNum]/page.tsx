import React from 'react';
import Page from '../../page';

export default async function PageCurrent (
    {
        params,
    }: {
        params: Promise<{
            pageNum: string;
        }>;
    },
) {
    return <Page params={params} />;
}
