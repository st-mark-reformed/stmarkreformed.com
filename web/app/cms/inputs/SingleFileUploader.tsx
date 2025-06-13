import React from 'react';
import { XMarkIcon } from '@heroicons/react/20/solid';
import DndSingleFileUploader from './DndSingleFileUploader';
import { ImageUploadFileTypes } from './ImageUploadFileTypes';

export default function SingleFileUploader (
    {
        label,
        name,
        value,
        setValue,
        fileTypes = ImageUploadFileTypes,
    }: {
        label: string;
        name: string;
        value: string;
        setValue: (key: string, val: string) => void;
        fileTypes?: Array<string>;
    },
) {
    const buttonClasses = [
        'text-sm',
        'leading-6',
        'relative',
        'cursor-pointer',
        'rounded-md',
        'bg-white',
        'font-semibold',
        'focus-within:outline-none',
        'border',
        'px-2',
    ];

    if (value) {
        buttonClasses.push(
            'text-cyan-600',
            'hover:text-cyan-700',
            'border-cyan-600',
            'hover:border-cyan-700',
            'hover:bg-gray-100',
        );
    } else {
        buttonClasses.push(
            'text-gray-300',
            'border-gray-300',
        );
    }

    return (
        <div>
            <div className="flex justify-between items-center gap-2">
                <label
                    htmlFor={name}
                    className="block text-sm font-semibold leading-6 text-gray-900"
                >
                    {label}
                </label>
                <button
                    type="button"
                    className={buttonClasses.join(' ')}
                    onClick={() => {
                        setValue(name, '');
                    }}
                >
                    <XMarkIcon className="h-4 w-4 mr-1 -mt-0.5 inline-block" />
                    Remove file
                </button>
            </div>
            <div className="mt-2">
                <DndSingleFileUploader
                    name={name}
                    fileUrl={value}
                    fileTypes={fileTypes}
                    setFileData={(fileData) => {
                        setValue(name, fileData);
                    }}
                />
            </div>
        </div>
    );
}
