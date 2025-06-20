import React from 'react';
import PortalOverlay from './layout/PortalOverlay';

export default function ConfirmDeleteOverlay (
    {
        closeOverlay,
        proceed,
        isDeleting,
        heading = 'Delete Selected?',
        body = 'This is a non-recoverable action. Do you wish to proceed?',
    }: {
        closeOverlay: () => void;
        proceed: () => void;
        isDeleting: boolean;
        heading?: string;
        body?: string;
    },
) {
    return (
        <PortalOverlay>
            {/* eslint-disable-next-line jsx-a11y/no-static-element-interactions */}
            <div
                className="bg-white shadow sm:rounded-lg text-left"
                onKeyDown={(e) => {
                    if (e.key === 'Escape') {
                        closeOverlay();
                    }

                    if (e.key !== 'Enter') {
                        return;
                    }

                    proceed();
                }}
            >
                <div className="px-4 py-5 sm:p-6 min-w-96">
                    <h3 className="text-base font-semibold leading-6 text-gray-900">
                        {heading}
                    </h3>
                    <div className="mt-2 text-left">
                        <p className="w-full mb-2 max-w-lg">
                            {body}
                        </p>
                        <div className="flex justify-between">
                            <button
                                type="button"
                                className="rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 cursor-pointer"
                                onClick={() => {
                                    closeOverlay();
                                }}
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                className={(() => {
                                    const classes = ['rounded-md px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm cursor-pointer'];

                                    if (isDeleting) {
                                        classes.push('bg-gray-300');
                                    } else {
                                        classes.push('bg-red-600 hover:bg-red-700');
                                    }

                                    return classes.join(' ');
                                })()}
                                onClick={proceed}
                                disabled={isDeleting}
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </PortalOverlay>
    );
}
