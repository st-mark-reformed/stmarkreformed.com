import React from 'react';
import GetMessages from './GetMessages';
import MessagesPageClientSide from './MessagesPageClientSide';

export default async function MessagesPage () {
    const messages = await GetMessages();

    return <MessagesPageClientSide messages={messages} />;
}
