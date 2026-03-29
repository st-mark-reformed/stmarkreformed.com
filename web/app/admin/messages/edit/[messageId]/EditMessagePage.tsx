import React from 'react';
import GetEditMessage from './GetEditMessage';
import EditMessagePageClientSide from './EditMessagePageClientSide';

export default async function EditMessagePage (
    {
        messageId,
    }: {
        messageId: string;
    },
) {
    const message = await GetEditMessage(messageId);

    if (!message) {
        return null;
    }

    return <EditMessagePageClientSide message={message} />;
}
