import React, { Dispatch, SetStateAction } from 'react';
import Link from 'next/link';
import { PencilIcon } from '@heroicons/react/24/solid';
import { Message } from './Message';

export default function MessageListingItem (
    {
        message,
        selectedIds,
        setSelectedIds,
    }: {
        message: Message;
        selectedIds: Array<string>;
        setSelectedIds: Dispatch<SetStateAction<Array<string>>>;
    },
) {
    const isSelected = selectedIds.indexOf(message.id) > -1;

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
                        selectedIds.indexOf(message.id),
                        1,
                    );
                } else {
                    newSelectedIds.push(message.id);
                }

                setSelectedIds(newSelectedIds);
            }}
        >
            <div className="flex min-w-0 gap-x-4 pl-0 flex-1">
                <div className="min-w-0 flex-auto">
                    <p className="text-sm font-semibold leading-6 text-gray-900">
                        {message.title || message.id}
                        {(() => {
                            if (!message.text) {
                                return null;
                            }

                            return (
                                <span className="text-gray-500 text-xs font-normal">
                                    {' '}
                                    ({message.text})
                                </span>
                            );
                        })()}
                    </p>
                    <p className="text-xs font-extralight leading-6 text-gray-900">
                        {message.dateDisplay}
                    </p>
                    {(() => {
                        if (!message.speaker) {
                            return null;
                        }

                        return (
                            <p className="text-xs font-extralight leading-6 text-gray-900 sm:hidden">
                                by: {message.speaker.fullNameWithHonorific}
                            </p>
                        );
                    })()}
                    {(() => {
                        if (!message.series) {
                            return null;
                        }

                        return (
                            <p className="text-xs font-extralight leading-6 text-gray-900 sm:hidden">
                                series: {message.series.title}
                            </p>
                        );
                    })()}
                </div>
            </div>
            <div className="hidden sm:flex gap-x-6 w-96">
                {(() => {
                    if (!message.speaker) {
                        return <div className="w-1/2" />;
                    }

                    return (
                        <div className="text-gray-600 text-sm w-1/2 truncate">
                            by: {message.speaker.fullNameWithHonorific}
                        </div>
                    );
                })()}
                {(() => {
                    if (!message.series) {
                        return <div className="w-1/2" />;
                    }

                    return (
                        <div className="text-gray-600 text-sm w-1/2 truncate">
                            series: {message.series.title}
                        </div>
                    );
                })()}
            </div>
            <div className="flex shrink-0 items-center gap-x-4">
                <div className="sm:flex sm:flex-col sm:items-end">
                    <div className="text-sm leading-6 text-gray-900">
                        <Link
                            data-prevent-select
                            href={`/cms/entries/messages-test/${message.id}`}
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
