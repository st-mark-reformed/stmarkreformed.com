import React from 'react';
import GetEditMailingList from './GetEditMailingList';
import EditMailingListPageClientSide from './EditMailingListPageClientSide';

export default async function EditMailingListPage (
    {
        mailingListId,
    }: {
        mailingListId: string;
    },
) {
    const mailingList = await GetEditMailingList(mailingListId);

    if (!mailingList) {
        return null;
    }

    return <EditMailingListPageClientSide mailingList={mailingList} />;
}
