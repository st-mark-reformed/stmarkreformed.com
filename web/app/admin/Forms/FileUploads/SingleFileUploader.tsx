import React, { useState } from 'react';
import { DocumentIcon } from '@heroicons/react/24/outline';
import InputWrapper from '../InputWrapper';

export default function SingleFileUploader (
    {
        label,
        name,
        fileTypes = ['MP3'],
        defaultValue = '',
        error = undefined,
    }: {
        label: string;
        name: string;
        fileTypes?: Array<string>;
        defaultValue?: string;
        error?: string | undefined;
    },
) {
    const inputRef = React.useRef<HTMLInputElement>(null);

    const [fileName, setFileName] = useState<string>(defaultValue);
    const [isDragging, setIsDragging] = useState(false);
    const [uploadError, setUploadError] = useState<string | undefined>(undefined);

    const allowedExtensions = fileTypes.map((type) => type.toLowerCase());
    const fileExtension = fileName ? fileName.split('.').pop() : null;

    function isAllowedFile (file: File) {
        const extension = file.name.split('.').pop()?.toLowerCase() ?? '';

        return allowedExtensions.length === 0 || allowedExtensions.includes(extension);
    }

    function handleFile (file: File | undefined) {
        setUploadError(undefined);

        if (!file) {
            return;
        }

        if (!isAllowedFile(file)) {
            setUploadError(`Invalid file type. Only ${fileTypes.join(', ')} files are allowed.`);

            return;
        }

        setFileName(file.name);
    }

    if (error && uploadError) {
        error = `${uploadError}. ${error}`;
    } else if (uploadError) {
        error = uploadError;
    }

    return (
        <div className="col-span-full relative">
            {(() => {
                if (!fileName) {
                    return null;
                }

                return (
                    <button
                        type="button"
                        className="absolute right-0 top-0 cursor-pointer rounded-sm bg-crimson-dark/10 dark:bg-crimson/50 px-2 py-1 text-xs font-semibold text-crimson dark:text-white/90 shadow-xs hover:bg-crimson-dark/20 dark:hover:bg-crimson/60"
                        onClick={() => {
                            setFileName('');
                            if (inputRef.current) {
                                inputRef.current.value = '';
                            }
                        }}
                    >
                        Remove File
                    </button>
                );
            })()}
            <InputWrapper label={label} name={name} colSpan="full" error={error}>
                <input
                    ref={inputRef}
                    type="file"
                    name={name}
                    accept={allowedExtensions.map((ext) => `.${ext}`).join(',')}
                    className="hidden"
                    onChange={(event) => {
                        handleFile(event.target.files?.[0]);
                    }}
                />
                <div
                    className={[
                        'text-gray-600 bg-white px-3 py-10 rounded-md border border-gray-300 dark:text-white dark:border-white/10 dark:bg-white/5 text-center text-sm transition-colors relative',
                        isDragging ? 'border-crimson bg-crimson/5 dark:bg-crimson/10' : '',
                    ].join(' ')}
                    onDragEnter={(event) => {
                        event.preventDefault();
                        event.stopPropagation();
                        setIsDragging(true);
                    }}
                    onDragOver={(event) => {
                        event.preventDefault();
                        event.stopPropagation();
                        setIsDragging(true);
                    }}
                    onDragLeave={(event) => {
                        event.preventDefault();
                        event.stopPropagation();
                        setIsDragging(false);
                    }}
                    onDrop={(event) => {
                        event.preventDefault();
                        event.stopPropagation();
                        setIsDragging(false);

                        const file = event.dataTransfer.files?.[0];

                        if (!file || !inputRef.current) {
                            return;
                        }

                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        inputRef.current.files = dataTransfer.files;

                        handleFile(file);
                    }}
                >
                    {(() => {
                        if (!isDragging) {
                            return null;
                        }

                        return (
                            <div className="w-full h-full bg-gray-300/95 absolute left-0 top-0 z-50 text-black flex items-center justify-center text-center">
                                Drop file here to upload
                            </div>
                        );
                    })()}
                    {(() => {
                        if (fileName) {
                            return <div><span className="font-semibold">Selected File</span>: {fileName}</div>;
                        }

                        return <div>Upload a file</div>;
                    })()}

                    <div className="relative my-3">
                        {(() => {
                            if (!fileName) {
                                return <DocumentIcon className="mx-auto w-24 text-gray-100" />;
                            }

                            return (
                                <>
                                    <DocumentIcon className="mx-auto w-24 text-crimson/50 dark:text-gray-100" />
                                    <div
                                        className="absolute bottom-3 left-0 right-0 text-sm font-semibold text-crimson/70 rounded-b-md px-2 py-1 mx-auto"
                                        style={{ width: '66px' }}
                                    >
                                        {fileExtension?.toUpperCase()}
                                    </div>
                                </>
                            );
                        })()}
                    </div>
                    <div className="mt-1">
                        Drag and Drop a {fileName ? 'new' : null} file here
                    </div>
                    <div className="mt-1 italic">
                        or
                    </div>
                    <button
                        type="button"
                        className="mt-2 cursor-pointer rounded-sm bg-crimson-dark/10 dark:bg-crimson/50 px-2 py-1 text-xs font-semibold text-crimson dark:text-white/90 shadow-xs hover:bg-crimson-dark/20 dark:hover:bg-crimson/60"
                        onClick={() => {
                            inputRef.current?.click();
                        }}
                    >
                        Select From Your Device
                    </button>
                </div>
            </InputWrapper>
        </div>
    );
}
