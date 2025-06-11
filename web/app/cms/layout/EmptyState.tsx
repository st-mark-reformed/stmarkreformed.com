import React, { BaseSyntheticEvent } from 'react';
import { PlusIcon } from '@heroicons/react/20/solid';
import { FolderPlusIcon } from '@heroicons/react/24/outline';
import Link from 'next/link';

export default function EmptyState (
    {
        buttonHref = '',
        onButtonClick,
        itemNameSingular = 'item',
        itemNamePlural = 'items',
    }: {
        buttonHref?: string;
        onButtonClick?: (event: BaseSyntheticEvent) => void;
        itemNameSingular?: string;
        itemNamePlural?: string;
    },
) {
    const buttonClasses = 'inline-flex items-center rounded-md bg-cyan-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-cyan-700 cursor-pointer';
    const iconClasses = '-ml-0.5 mr-1.5 h-5 w-5';

    return (
        <div className="text-center rounded-lg border-2 border-dashed border-gray-300 p-6">
            <FolderPlusIcon className="mx-auto h-12 w-12 text-gray-400" />
            <h3 className="mt-2 text-sm font-semibold text-gray-900">
                No
                {' '}
                {itemNamePlural}
            </h3>
            <p className="mt-1 text-sm text-gray-500">
                Get started by creating a new
                {' '}
                {itemNameSingular}
                .
            </p>
            {(() => {
                if (!onButtonClick && !buttonHref) {
                    return null;
                }

                return (
                    <div className="mt-6">
                        {(() => {
                            if (onButtonClick) {
                                return (
                                    <button
                                        type="button"
                                        className={buttonClasses}
                                        onClick={onButtonClick}
                                    >
                                        <PlusIcon className={iconClasses} aria-hidden="true" />
                                        New
                                        {' '}
                                        {itemNameSingular}
                                    </button>
                                );
                            }

                            return (
                                <Link
                                    className={buttonClasses}
                                    href={buttonHref}
                                >
                                    <PlusIcon className={iconClasses} aria-hidden="true" />
                                    New
                                    {' '}
                                    {itemNameSingular}
                                </Link>
                            );
                        })()}
                    </div>
                );
            })()}
        </div>
    );
}
