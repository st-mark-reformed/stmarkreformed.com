import React, { Dispatch, SetStateAction } from 'react';
import Link from 'next/link';
import { ArrowTopRightOnSquareIcon } from '@heroicons/react/16/solid';
import { ArrowDownOnSquareIcon } from '@heroicons/react/24/outline';
import { File } from './File';

export default function FileListingItem (
    {
        file,
        selectedNames,
        setSelectedNames,
    }: {
        file: File;
        selectedNames: Array<string>;
        setSelectedNames: Dispatch<SetStateAction<Array<string>>>;
    },
) {
    const isSelected = selectedNames.indexOf(file.filename) > -1;

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

                const newSelectedNames = [...selectedNames];

                if (isSelected) {
                    newSelectedNames.splice(
                        selectedNames.indexOf(file.filename),
                        1,
                    );
                } else {
                    newSelectedNames.push(file.filename);
                }

                setSelectedNames(newSelectedNames);
            }}
        >
            <div className="flex min-w-0 gap-x-4 pl-0">
                <div className="min-w-0 flex-auto">
                    <p className="text-sm font-semibold leading-6 text-gray-900">
                        {file.filename}
                    </p>
                    <p className="text-xs font-extralight leading-6 text-gray-900">
                        {file.size}
                    </p>
                </div>
            </div>
            <div className="flex shrink-0 items-center gap-x-4">
                <div className="sm:flex sm:flex-col sm:items-end">
                    <div className="text-sm leading-6 text-gray-900">
                        <Link
                            data-prevent-select
                            href={`/uploads/audio/${file.filename}`}
                            className="rounded bg-cyan-600 px-2 py-1 text-xs font-semibold text-white shadow-sm hover:bg-cyan-700 ml-4"
                            download
                        >
                            <ArrowDownOnSquareIcon className="h-3 w-3 text-white inline -mt-0.5" />
                            {' '}
                            Download File
                        </Link>
                        <Link
                            data-prevent-select
                            href={`/uploads/audio/${file.filename}`}
                            className="rounded bg-cyan-600 px-2 py-1 text-xs font-semibold text-white shadow-sm hover:bg-cyan-700 ml-4"
                            target="_blank"
                        >
                            <ArrowTopRightOnSquareIcon className="h-3 w-3 text-white inline -mt-0.5" />
                            {' '}
                            Open File
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
