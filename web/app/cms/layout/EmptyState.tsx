import React, { BaseSyntheticEvent } from 'react';
import { PlusIcon } from '@heroicons/react/20/solid';
import { FolderPlusIcon } from '@heroicons/react/24/outline';

export default function EmptyState (
    {
        onButtonClick,
        itemNameSingular = 'item',
        itemNamePlural = 'items',
    }: {
        onButtonClick?: (event: BaseSyntheticEvent) => void;
        itemNameSingular?: string;
        itemNamePlural?: string;
    },
) {
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
            <div className="mt-6">
                <button
                    type="button"
                    className="inline-flex items-center rounded-md bg-cyan-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-cyan-700 cursor-pointer"
                    onClick={onButtonClick}
                >
                    <PlusIcon className="-ml-0.5 mr-1.5 h-5 w-5" aria-hidden="true" />
                    New
                    {' '}
                    {itemNameSingular}
                </button>
            </div>
        </div>
    );
}
