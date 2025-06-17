import React, { Dispatch, SetStateAction } from 'react';
import Link from 'next/link';
import { PencilIcon } from '@heroicons/react/24/solid';
import { MessageSeries } from './MessageSeries';

export default function MessageSeriesListingItem (
    {
        series,
        selectedIds,
        setSelectedIds,
    }: {
        series: MessageSeries;
        selectedIds: Array<string>;
        setSelectedIds: Dispatch<SetStateAction<Array<string>>>;
    },
) {
    const isSelected = selectedIds.indexOf(series.id) > -1;

    return (
        // eslint-disable-next-line jsx-a11y/click-events-have-key-events,jsx-a11y/no-noninteractive-element-interactions
        <li
            className={(() => {
                const classes = ['relative flex justify-between gap-x-6 px-4 py-5 sm:px-6'];

                if (isSelected) {
                    classes.push('bg-cyan-600/5');
                }

                return classes.join(' ');
            })()}
            onClick={(e) => {
                const element = e.target as HTMLElement;

                if (element.dataset.preventSelect) {
                    return;
                }

                const newSelectedIds = [...selectedIds];

                if (isSelected) {
                    newSelectedIds.splice(
                        selectedIds.indexOf(series.id),
                        1,
                    );
                } else {
                    newSelectedIds.push(series.id);
                }

                setSelectedIds(newSelectedIds);
            }}
        >
            <div className="flex min-w-0 gap-x-4 pl-0">
                <div className="min-w-0 flex-auto">
                    <p className="text-sm font-semibold leading-6 text-gray-900">
                        {series.title}
                    </p>
                    <p className="text-xs font-extralight leading-6 text-gray-900">
                        {series.slug}
                    </p>
                </div>
            </div>
            <div className="flex shrink-0 items-center gap-x-4">
                <div className="sm:flex sm:flex-col sm:items-end">
                    <div className="text-sm leading-6 text-gray-900">
                        <Link
                            data-prevent-select
                            href={`/cms/entries/messages-test/series-manager/${series.id}`}
                            className="rounded bg-cyan-600 px-2 py-1 text-xs font-semibold text-white shadow-sm hover:bg-cyan-700 ml-4"
                        >
                            <PencilIcon className="h-3 w-3 text-white inline -mt-0.5" />
                            {' '}
                            Edit
                        </Link>
                        <input
                            type="checkbox"
                            className="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-600 ml-4"
                            checked={isSelected}
                            onChange={() => {}}
                        />
                    </div>
                </div>
            </div>
        </li>
    );
}
