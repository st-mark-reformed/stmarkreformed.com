'use client';

import { useEffect, useRef } from 'react';

export default function KeepAlive (
    {
        intervalMs = 60_000,
    }: {
        intervalMs?: number;
    },
) {
    const inFlightRef = useRef(false);

    useEffect(() => {
        let cancelled = false;

        const pingRefreshEndpoint = async () => {
            if (cancelled || inFlightRef.current) return;

            inFlightRef.current = true;

            try {
                await fetch('/api/auth/keep-alive', {
                    cache: 'no-store',
                });
            } finally {
                inFlightRef.current = false;
            }
        };

        const timer = window.setInterval(() => {
            pingRefreshEndpoint();
        }, intervalMs);

        return () => {
            cancelled = true;
            window.clearInterval(timer);
        };
    }, [intervalMs]);

    return null;
}
