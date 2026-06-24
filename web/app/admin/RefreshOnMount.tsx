'use client';

import { useEffect } from 'react';
import { useRouter } from 'next/navigation';

/**
 * Forces the current route's server data to refetch once on mount.
 *
 * A Server Action that calls `revalidatePath` and then `redirect` to a list
 * page does not reliably clear the client Router Cache for that destination, so
 * the soft navigation can show the list as it was cached when the create/edit
 * page was opened (missing the just-saved item). Refreshing on mount reconciles
 * the list against the server. Client state is preserved across the refresh.
 */
export default function RefreshOnMount () {
    const router = useRouter();

    useEffect(() => {
        router.refresh();
    }, [router]);

    return null;
}
