import React from 'react';
import QueueSidebarNotesCSR from './QueueSidebarNotesCSR';
import { GetQueueStatus } from '../../queue/status/route';

export default async function QueueSidebarNotes () {
    const initialStatus = await GetQueueStatus();

    return (
        <QueueSidebarNotesCSR initialStatus={initialStatus} />
    );
}
