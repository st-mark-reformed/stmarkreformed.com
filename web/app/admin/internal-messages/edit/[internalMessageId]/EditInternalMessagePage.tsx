import React from 'react';
import GetEditInternalMessage from './GetEditInternalMessage';
import EditInternalMessagePageClientSide from './EditInternalMessagePageClientSide';

export default async function EditInternalMessagePage (
    {
        internalMessageId,
    }: {
        internalMessageId: string;
    },
) {
    const message = await GetEditInternalMessage(internalMessageId);

    if (!message) {
        return null;
    }

    return <EditInternalMessagePageClientSide message={message} />;
}
