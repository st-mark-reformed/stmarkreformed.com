'use client';

import React from 'react';
import { Message } from '../../Message';
import CreateEditMessagePage from '../../CreateEditMessagePage';

export default function EditMessagePageClientSide (
    {
        message,
    }: {
        message: Message;
    },
) {
    return (
        <CreateEditMessagePage
            pageTitle={`Edit Message: ${message.title}`}
            submitFormAction="edit"
            initialValues={{
                ...message,
                speakerId: message.speaker.id,
                seriesId: message.series.id,
            }}
            messageId={message.id}
        />
    );
}
