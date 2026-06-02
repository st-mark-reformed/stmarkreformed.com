'use client';

import React from 'react';
import { InternalMessage } from '../../InternalMessage';
import CreateEditInternalMessagePage from '../../CreateEditInternalMessagePage';

export default function EditInternalMessagePageClientSide (
    {
        message,
    }: {
        message: InternalMessage;
    },
) {
    return (
        <CreateEditInternalMessagePage
            pageTitle={`Edit Internal Message: ${message.title}`}
            submitFormAction="edit"
            initialValues={{
                ...message,
                speakerId: message.speaker.id,
                seriesId: message.series.id,
            }}
            internalMessageId={message.id}
        />
    );
}
