import React from 'react';
import GetSchedule from './GetSchedule';
import Breadcrumbs from '../Breadcrumbs';
import PageTitle from '../PageTitle';

export default async function SchedulePage () {
    const schedule = await GetSchedule();

    return (
        <>
            <Breadcrumbs />
            <PageTitle>Schedule</PageTitle>
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
                                            Run Every
                                        </th>
                                        <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                            Class
                                        </th>
                                        <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                            Method
                                        </th>
                                        <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                            Last Run Start At
                                        </th>
                                        <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                            Last Run End At
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white dark:bg-gray-900">
                                    {schedule.map((item) => (
                                        <tr
                                            key={`${item.runEvery}-${item.class}-${item.method}`}
                                            className="even:bg-gray-50 dark:even:bg-gray-800/50"
                                        >
                                            <td className="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-3 dark:text-white">
                                                {item.runEvery}
                                            </td>
                                            <td className="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                {item.class}
                                            </td>
                                            <td className="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                {item.method}
                                            </td>
                                            <td className="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                {item.lastRunStartAt}
                                            </td>
                                            <td className="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                {item.lastRunEndAt}
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
