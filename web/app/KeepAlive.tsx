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
    const redirectingRef = useRef(false);

    useEffect(() => {
        let cancelled = false;

        const pingRefreshEndpoint = async () => {
            if (cancelled || inFlightRef.current || redirectingRef.current) return;

            inFlightRef.current = true;

            try {
                const response = await fetch('/api/auth/keep-alive', {
                    cache: 'no-store',
                });

                if (response.status === 401) {
                    redirectingRef.current = true;

                    const returnTo = encodeURIComponent(window.location.href);

                    /**
                     * Use a full-page navigation rather than `router.push` —
                     * the auth state has changed and we want to drop any stale
                     * React/SWC state on the floor.
                     */
                    window.location.href = `/admin/session-expired?returnTo=${returnTo}`;
                }
            } catch {
                // Network error — not an auth failure
            } finally {
                inFlightRef.current = false;
            }
        };

        /**
         * When the tab becomes visible again (e.g. user returns from another
         * tab, or the laptop wakes from sleep), ping immediately instead of
         * waiting up to a full interval. `setInterval` is heavily throttled or
         * paused in background tabs, so without this the page can sit in a
         * broken state for up to `intervalMs` after wake.
         */
        const handleVisibilityChange = () => {
            if (document.visibilityState === 'visible') {
                pingRefreshEndpoint();
            }
        };

        const timer = window.setInterval(() => {
            pingRefreshEndpoint();
        }, intervalMs);

        document.addEventListener('visibilitychange', handleVisibilityChange);

        return () => {
            cancelled = true;
            window.clearInterval(timer);
            document.removeEventListener('visibilitychange', handleVisibilityChange);
        };
    }, [intervalMs]);

    return null;
}
