import React from 'react';
import GetResources from './GetResources';
import ResourcesPageClientSide from './ResourcesPageClientSide';

export default async function ResourcesPage (
    {
        pageNum,
        keyword,
    }: {
        pageNum: number;
        keyword: string;
    },
) {
    const pageData = await GetResources(pageNum, keyword);

    return (
        <ResourcesPageClientSide
            resourceItems={pageData.entries}
            currentPage={pageData.currentPage}
            totalPages={pageData.totalPages}
            keyword={keyword}
        />
    );
}
