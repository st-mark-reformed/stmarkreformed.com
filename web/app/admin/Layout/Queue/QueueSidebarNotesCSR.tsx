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
        let cancelled = false;

        const interval = setInterval(async () => {
            try {
                const resp = await fetch('/admin/queue/status');

                /**
                 * The session has expired out from under us. Drop everything
                 * and do a full-page navigation to the session-expired page,
                 * mirroring `KeepAlive` — don't keep polling or render the
                 * auth-error body as if it were a real queue status.
                 */
                if (resp.status === 401) {
                    const returnTo = encodeURIComponent(window.location.href);

                    window.location.href = `/admin/session-expired?returnTo=${returnTo}`;

                    return;
                }

                if (cancelled || !resp.ok) {
                    return;
                }

                const json = await resp.json() as unknown as QueueStatus;

                setStatus(json);
            } catch (e) { /* empty */ }
        }, 5000);

        return () => {
            cancelled = true;
            clearInterval(interval);
        };
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
