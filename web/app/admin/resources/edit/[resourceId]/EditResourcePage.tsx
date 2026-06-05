import React from 'react';
import GetEditResource from './GetEditResource';
import EditResourcePageClientSide from './EditResourcePageClientSide';

export default async function EditResourcePage (
    {
        resourceId,
    }: {
        resourceId: string;
    },
) {
    const resourceItem = await GetEditResource(resourceId);

    if (!resourceItem) {
        return null;
    }

    return <EditResourcePageClientSide resourceItem={resourceItem} />;
}
