'use client';

import React, { ReactNode, useState, useTransition } from 'react';
import PageTitle, { Button } from '../../PageTitle';
import RetryAllFailedQueueItemsAction from './RetryAllFailedQueueItemsAction';
import ClearAllFailedQueueItemsAction from './ClearAllFailedQueueItemsAction';

type BulkAction = 'retry' | 'clear';

export default function FailedQueuePageTitle (
    {
        children,
        hasFailedItems,
    }: {
        children: ReactNode;
        hasFailedItems: boolean;
    },
) {
    const [isPending, startTransition] = useTransition();
    const [runningAction, setRunningAction] = useState<BulkAction | null>(null);

    const run = (action: BulkAction) => {
        setRunningAction(action);

        startTransition(async () => {
            if (action === 'retry') {
                await RetryAllFailedQueueItemsAction();
            } else {
                await ClearAllFailedQueueItemsAction();
            }

            setRunningAction(null);
        });
    };

    const buttons: Button[] = [];

    if (hasFailedItems) {
        buttons.push(
            {
                content: isPending && runningAction === 'retry' ? 'Retrying…' : 'Retry All',
                href: 'retry-all-button',
                type: isPending ? 'pending' : 'primary',
                onClick: () => {
                    if (isPending) {
                        return;
                    }

                    run('retry');
                },
            },
            {
                content: isPending && runningAction === 'clear' ? 'Clearing…' : 'Clear All',
                glyph: 'trash',
                href: 'clear-all-button',
                type: isPending ? 'pending' : 'secondary',
                onClick: () => {
                    if (isPending) {
                        return;
                    }

                    run('clear');
                },
            },
        );
    }

    return <PageTitle buttons={buttons}>{children}</PageTitle>;
}
