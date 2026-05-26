'use client';

import React from 'react';
import { useFormStatus } from 'react-dom';
import RetryFailedQueueItemAction from './RetryFailedQueueItemAction';

function SubmitButton () {
    const { pending } = useFormStatus();

    return (
        <button
            type="submit"
            disabled={pending}
            className="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-semibold shadow-xs cursor-pointer bg-white text-gray-900 inset-ring inset-ring-gray-300 hover:bg-gray-50 disabled:cursor-default disabled:opacity-50 dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20"
        >
            {pending ? 'Retrying…' : 'Retry'}
        </button>
    );
}

export default function RetryButton ({ itemKey }: { itemKey: string }) {
    return (
        <form action={RetryFailedQueueItemAction}>
            <input type="hidden" name="key" value={itemKey} />
            <SubmitButton />
        </form>
    );
}
