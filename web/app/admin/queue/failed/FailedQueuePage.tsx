import React from 'react';
import GetFailedQueue from './GetFailedQueue';
import Breadcrumbs from '../../Breadcrumbs';
import PageTitle from '../../PageTitle';
import AutoRefresh from '../../../AutoRefresh';

export default async function FailedQueuePage () {
    const failedItems = await GetFailedQueue();

    return (
        <>
            <AutoRefresh />
            <Breadcrumbs
                crumbs={[
                    { content: 'Queue', href: '/admin/queue' },
                    { content: 'Failed', href: '/admin/queue/failed' },
                ]}
            />
            <PageTitle
                buttons={[{
                    content: 'View Queue',
                    href: '/admin/queue',
                    type: 'secondary',
                }]}
            >
                Failed Queue Items{' '}
                <span className="text-sm font-normal">(refreshes every 5 seconds)</span>
            </PageTitle>
            {(() => {
                if (failedItems.length === 0) {
                    return (
                        <div className="p-4 bg-white rounded-xl shadow-sm border border-gray-200">
                            <p className="text-sm text-gray-500">No failed items.</p>
                        </div>
                    );
                }

                return (
                    <div className="flex flex-col gap-4">
                        {failedItems.map((entry) => (
                            <div
                                key={entry.key}
                                className="p-4 bg-white rounded-xl shadow-sm border border-red-200"
                            >
                                <div className="flex items-start justify-between gap-4 mb-3">
                                    <div>
                                        <p className="text-sm font-semibold text-gray-900">
                                            {entry.queueItem.name}
                                        </p>
                                        <p className="text-xs text-gray-500 mt-0.5">
                                            {entry.queueItem.handle}
                                        </p>
                                    </div>
                                    <div className="flex items-center gap-2 shrink-0">
                                        {entry.retried && (
                                            <span className="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">
                                                Retried
                                            </span>
                                        )}
                                        <span className="text-xs text-gray-400">
                                            {new Date(entry.date).toLocaleString()}
                                        </span>
                                    </div>
                                </div>
                                <div className="mb-3 rounded-md bg-red-50 p-3 text-sm text-red-700">
                                    <p className="font-medium">{entry.message}</p>
                                    <p className="mt-1 text-xs text-red-500">
                                        {entry.file}:{entry.line}
                                        {entry.code !== 0 && (
                                            <span className="ml-2">(code {entry.code})</span>
                                        )}
                                    </p>
                                </div>
                                <details className="group">
                                    <summary className="cursor-pointer text-xs font-medium text-gray-500 hover:text-gray-700 select-none">
                                        Stack trace
                                    </summary>
                                    <pre className="mt-2 overflow-x-auto rounded-md bg-gray-50 p-3 text-xs text-gray-700 whitespace-pre-wrap break-all">
                                        {entry.trace}
                                    </pre>
                                </details>
                                {entry.queueItem.jobs.length > 0 && (
                                    <div className="mt-3 border-t border-gray-100 pt-3">
                                        <p className="text-xs font-medium text-gray-500 mb-1">Jobs</p>
                                        {entry.queueItem.jobs.map((job) => (
                                            <p
                                                key={`${job.class}-${job.method}`}
                                                className="text-xs text-gray-500"
                                            >
                                                {job.class}::{job.method}
                                            </p>
                                        ))}
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>
                );
            })()}
        </>
    );
}
