'use client';

import React, { useEffect, useState } from 'react';
import { QueueStatus } from '../../queue/status/GetQueueStatus';

export default function QueueSidebarNotesCSR (
    {
        initialStatus,
    }: {
        initialStatus: QueueStatus;
    },
) {
    const [status, setStatus] = useState<QueueStatus>(initialStatus);

    useEffect(() => {
        const interval = setInterval(async () => {
            try {
                const resp = await fetch('/admin/queue/status');

                const json = await resp.json() as unknown as QueueStatus;

                setStatus(json);
            } catch (e) { /* empty */ }
        }, 5000);

        return () => clearInterval(interval);
    }, []);

    return (
        <>
            <span className="text-gray-400">({status.enqueued})</span>
            {(() => {
                if (status.failed < 1) {
                    return null;
                }

                return <span className="text-red-600">({status.failed} failed)</span>;
            })()}
        </>
    );
}
