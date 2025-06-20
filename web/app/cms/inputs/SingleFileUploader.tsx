import React, { useState } from 'react';
import { XMarkIcon } from '@heroicons/react/20/solid';
import { DocumentPlusIcon } from '@heroicons/react/24/outline';
import { createPortal } from 'react-dom';
import DndSingleFileUploader from './DndSingleFileUploader';
import { ImageUploadFileTypes } from './ImageUploadFileTypes';
import SingleFileUploaderSelectExistingFile from './SingleFileUploaderSelectExistingFile';

export default function SingleFileUploader (
    {
        label,
        name,
        value,
        setValue,
        fileTypes = ImageUploadFileTypes,
        filePickerFileNames = [],
    }: {
        label: string;
        name: string;
        value: string;
        setValue: (key: string, val: string) => void;
        fileTypes?: Array<string>;
        filePickerFileNames?: Array<string>;
    },
) {
    const [selectIsOpen, setSelectIsOpen] = useState(false);

    const baseButtonClasses = ['text-sm leading-6 relative cursor-pointer rounded-md bg-white font-semibold focus-within:outline-none border px-2 ml-2'];

    const activeButtonClasses = ['text-cyan-600 hover:text-cyan-700 border-cyan-600 hover:border-cyan-700 hover:bg-gray-100'];

    const inactiveButtonButtonClasses = ['text-gray-300 border-gray-300'];

    return (
        <div>
            {(() => {
                if (!selectIsOpen) {
                    return null;
                }

                return createPortal(
                    <SingleFileUploaderSelectExistingFile
                        close={() => setSelectIsOpen(false)}
                        filePickerFileNames={filePickerFileNames}
                        current={value}
                        set={(val: string) => {
                            setValue(name, val);
                        }}
                    />,
                    document.body,
                );
            })()}
            <div className="flex justify-between items-center gap-2">
                <label
                    htmlFor={name}
                    className="block text-sm font-semibold leading-6 text-gray-900"
                >
                    {label}
                </label>
                <div className="text-right">
                    {(() => {
                        if (filePickerFileNames.length < 1) {
                            return null;
                        }

                        return (
                            <button
                                type="button"
                                className={(() => {
                                    const buttonClasses = [
                                        ...baseButtonClasses,
                                        ...activeButtonClasses,
                                    ];

                                    return buttonClasses.join(' ');
                                })()}
                                onClick={() => setSelectIsOpen(true)}
                            >
                                <DocumentPlusIcon className="h-4 w-4 mr-1 -mt-1 inline-block" />
                                Select Existing
                            </button>
                        );
                    })()}
                    <button
                        type="button"
                        className={(() => {
                            const buttonClasses = baseButtonClasses;

                            if (value) {
                                buttonClasses.push(...activeButtonClasses);
                            } else {
                                buttonClasses.push(...inactiveButtonButtonClasses);
                            }

                            return buttonClasses.join(' ');
                        })()}
                        onClick={() => {
                            setValue(name, '');
                        }}
                    >
                        <XMarkIcon className="h-4 w-4 mr-1 -mt-0.5 inline-block" />
                        Remove file
                    </button>
                </div>
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
