import React, { useEffect, useState } from 'react';
import { XMarkIcon } from '@heroicons/react/24/outline';
import PortalOverlay from '../layout/PortalOverlay';

export default function SingleFileUploaderSelectExistingFile (
    {
        close,
        set,
        current,
        filePickerFileNames,
    }: {
        close: () => void;
        set: (val: string) => void;
        current: string;
        filePickerFileNames: Array<string>;
    },
) {
    useEffect(() => {
        const handleKeyDown = (event: KeyboardEvent) => {
            if (event.key === 'Escape') {
                close();
            }
        };

        document.addEventListener('keydown', handleKeyDown);

        return () => document.removeEventListener('keydown', handleKeyDown);
    }, [close]);

    const [filterText, setFilterText] = useState('');

    if (filterText) {
        filePickerFileNames = filePickerFileNames.filter(
            (file) => file.toLowerCase().includes(filterText.toLowerCase()),
        );
    }

    return (
        <PortalOverlay>
            <div className="bg-white shadow sm:rounded-lg text-left">
                <div className="px-4 py-5 sm:p-6 min-w-xl">
                    <div className="relative mb-4">
                        <button
                            type="button"
                            className="rounded-sm hover:bg-gray-300 absolute right-1 top-0 cursor-pointer"
                            onClick={() => close()}
                        >
                            <XMarkIcon className="text-gray-900 h-6 w-6" />
                        </button>
                        <h3 className="text-base font-semibold leading-6 text-gray-900">
                            Choose existing file
                        </h3>
                    </div>
                    <div className="mb-4">
                        <input
                            type="text"
                            placeholder="Search"
                            className="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-inset focus:ring-gray-400 sm:text-sm sm:leading-6"
                            value={filterText}
                            onChange={(e) => setFilterText(e.target.value)}
                        />
                    </div>
                    <div className="mt-2 text-left">
                        <ul className="divide-y divide-gray-100 overflow-hidden bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl">
                            {filePickerFileNames.map((file) => {
                                if (file === current) {
                                    return (
                                        <li
                                            key={file}
                                            className="relative flex justify-between gap-x-6 px-4 py-5 sm:px-6 bg-bronze-lightened-2 text-white"
                                        >
                                            {file}
                                        </li>
                                    );
                                }

                                return (
                                    // eslint-disable-next-line jsx-a11y/click-events-have-key-events,jsx-a11y/no-noninteractive-element-interactions
                                    <li
                                        key={file}
                                        className="relative flex justify-between gap-x-6 px-4 py-5 sm:px-6 cursor-pointer hover:bg-cyan-100"
                                        onClick={() => {
                                            set(file);
                                            close();
                                        }}
                                    >
                                        {file}
                                    </li>
                                );
                            })}
                        </ul>
                    </div>
                </div>
            </div>
        </PortalOverlay>
    );
}
