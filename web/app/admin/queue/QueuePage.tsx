import React from 'react';
import GetQueue from './GetQueue';
import Breadcrumbs from '../Breadcrumbs';
import PageTitle from '../PageTitle';
import AutoRefresh from '../../AutoRefresh';

export default async function QueuePage () {
    const queueItems = await GetQueue();

    return (
        <>
            <AutoRefresh />
            <Breadcrumbs />
            <PageTitle>
                Queue <span className="text-sm font-normal">(refreshes every 5 seconds)</span>
            </PageTitle>
            <div className="p-4 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div className="flow-root">
                    <div className="-mx-4 -my-2 overflow-x-auto">
                        <div className="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table className="relative min-w-full divide-y divide-gray-300 dark:divide-white/15">
                                <thead>
                                    <tr>
                                        <th
                                            scope="col"
                                            className="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-3 dark:text-white"
                                        >
                                            Key
                                        </th>
                                        <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                            Handle
                                        </th>
                                        <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                            Name
                                        </th>
                                        <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                            Jobs
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white dark:bg-gray-900">
                                    {(() => {
                                        if (queueItems.length > 0) {
                                            return null;
                                        }

                                        return (
                                            <tr className="even:bg-gray-50 dark:even:bg-gray-800/50">
                                                <td className="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-3 dark:text-white">
                                                    No items in queue.
                                                </td>
                                            </tr>
                                        );
                                    })()}
                                    {queueItems.map((item) => (
                                        <tr
                                            key={`${item.key}-${item.handle}-${item.name}`}
                                            className="even:bg-gray-50 dark:even:bg-gray-800/50"
                                        >
                                            <td className="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-3 dark:text-white">
                                                {item.key}
                                            </td>
                                            <td className="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                {item.handle}
                                            </td>
                                            <td className="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                {item.name}
                                            </td>
                                            <td className="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                {item.jobs.map((job) => (
                                                    <div key={`${job.class}-${job.method}`}>
                                                        {job.class}::{job.method}
                                                    </div>
                                                ))}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
