import React from 'react';
import GetEditPastorsPage from './GetEditPastorsPage';
import EditPastorsPagePageClientSide from './EditPastorsPagePageClientSide';

export default async function EditPastorsPagePage (
    {
        pastorsPageId,
    }: {
        pastorsPageId: string;
    },
) {
    const pastorsPageItem = await GetEditPastorsPage(pastorsPageId);

    if (!pastorsPageItem) {
        return null;
    }

    return <EditPastorsPagePageClientSide pastorsPageItem={pastorsPageItem} />;
}
