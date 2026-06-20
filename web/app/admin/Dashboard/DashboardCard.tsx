import React from 'react';
import Link from 'next/link';
import { PlusIcon } from '@heroicons/react/16/solid';
import { ChevronRightIcon } from '@heroicons/react/20/solid';
import { AdminArea } from '../adminAreas';
import { DashboardRecentEntry } from './DashboardRecentEntry';
import { areaHasRecentList } from './recentEntryProviders';
import NavItemIconRenderer from '../Layout/NavItemIconRenderer';

export default function DashboardCard (
    {
        area,
        recent,
    }: {
        area: AdminArea;
        recent: DashboardRecentEntry[];
    },
) {
    return (
        <div className="flex flex-col rounded-lg border border-gray-200 bg-white shadow-xs dark:border-white/10 dark:bg-gray-800">
            <div className="flex items-center justify-between gap-3 border-b border-gray-100 p-4 dark:border-white/10">
                <Link
                    href={area.href}
                    className="flex items-center gap-3 text-gray-900 hover:text-crimson dark:text-white dark:hover:text-crimson"
                >
                    <NavItemIconRenderer icon={area.icon} />
                    <span className="text-lg font-semibold">{area.name}</span>
                </Link>
                {(() => {
                    if (!area.createHref) {
                        return null;
                    }

                    return (
                        <Link
                            href={area.createHref}
                            className="inline-flex shrink-0 items-center rounded-md bg-crimson px-2.5 py-1.5 pr-3 text-sm font-semibold text-white shadow-xs select-none hover:bg-crimson-dark dark:bg-crimson/70 dark:shadow-none dark:hover:bg-crimson/80"
                        >
                            <PlusIcon className="size-5 mr-1 -ml-0.5" aria-hidden="true" />
                            Create new
                        </Link>
                    );
                })()}
            </div>
            <div className="flex grow flex-col p-4">
                {(() => {
                    if (!areaHasRecentList(area.key)) {
                        return null;
                    }

                    if (recent.length === 0) {
                        return (
                            <p className="text-sm text-gray-500 dark:text-gray-400">
                                No entries yet.
                            </p>
                        );
                    }

                    return (
                        <ul className="space-y-2">
                            {recent.map((entry) => (
                                <li key={entry.id}>
                                    <Link
                                        href={entry.editHref}
                                        className="block rounded-md px-2 py-1 -mx-2 hover:bg-crimson/10"
                                    >
                                        <span className="block text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {entry.title}
                                        </span>
                                        {(() => {
                                            if (!entry.meta) {
                                                return null;
                                            }

                                            return (
                                                <span className="block text-xs text-gray-500 dark:text-gray-400">
                                                    {entry.meta}
                                                </span>
                                            );
                                        })()}
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    );
                })()}
            </div>
            <div className="border-t border-gray-100 p-4 dark:border-white/10">
                <Link
                    href={area.href}
                    className="inline-flex items-center text-sm font-semibold text-crimson hover:text-crimson-dark dark:text-crimson/90 dark:hover:text-crimson"
                >
                    {areaHasRecentList(area.key) ? `View all ${area.name}` : `Open ${area.name}`}
                    <ChevronRightIcon className="size-4 ml-0.5" aria-hidden="true" />
                </Link>
            </div>
        </div>
    );
}
