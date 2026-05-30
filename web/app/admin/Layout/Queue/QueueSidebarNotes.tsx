import React from 'react';
import QueueSidebarNotesCSR from './QueueSidebarNotesCSR';
import { GetQueueStatus } from '../../queue/status/GetQueueStatus';

export default async function QueueSidebarNotes () {
    const { queueStatus } = await GetQueueStatus();

    return (
        <QueueSidebarNotesCSR initialStatus={queueStatus} />
    );
}
