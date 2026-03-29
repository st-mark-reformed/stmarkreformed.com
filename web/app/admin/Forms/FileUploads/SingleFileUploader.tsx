import React from 'react';
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
    const [filePath, setFilePath] = React.useState<string>(defaultValue);

    const fileExtension = filePath ? filePath.split('.').pop() : null;

    return (
        <div className="col-span-full relative">
            {(() => {
                if (!filePath) {
                    return null;
                }

                return (
                    <button
                        type="button"
                        className="absolute right-0 top-0 cursor-pointer rounded-sm bg-crimson-dark/10 dark:bg-crimson/50 px-2 py-1 text-xs font-semibold text-crimson dark:text-white/90 shadow-xs hover:bg-crimson-dark/20 dark:hover:bg-crimson/60"
                        onClick={() => {
                            setFilePath('');
                        }}
                    >
                        Remove File
                    </button>
                );
            })()}
            <InputWrapper label={label} name={name} colSpan="full" error={error}>
                <input
                    type="hidden"
                    name={name}
                    value={filePath}
                />
                <div className="text-gray-600 bg-white px-3 py-10 rounded-md border border-gray-300 dark:text-white dark:border-white/10 dark:bg-white/5 text-center text-sm">
                    {(() => {
                        if (filePath.endsWith('.mp3')) {
                            return <div>Uploaded File: {filePath}</div>;
                        } if (filePath) {
                            return <div>File ready to be submitted</div>;
                        }

                        return <div>Upload a file</div>;
                    })()}
                    {(() => {
                        if (!filePath) {
                            return <DocumentIcon className="mx-auto w-24 text-gray-100" />;
                        }

                        return (
                            <div className="relative my-3">
                                <DocumentIcon className="mx-auto w-24 text-crimson/50 dark:text-gray-100" />
                                <div
                                    className="absolute bottom-3 left-0 right-0 text-sm font-semibold text-crimson/70 rounded-b-md px-2 py-1 mx-auto"
                                    style={{ width: '66px' }}
                                >
                                    {fileExtension?.toUpperCase()}
                                </div>
                            </div>
                        );
                    })()}
                    <div className="mt-1">
                        Drag and Drop a {filePath ? 'new' : null} file here
                    </div>
                    <div className="mt-1 italic">
                        or
                    </div>
                    <button
                        type="button"
                        className="mt-2 cursor-pointer rounded-sm bg-crimson-dark/10 dark:bg-crimson/50 px-2 py-1 text-xs font-semibold text-crimson dark:text-white/90 shadow-xs hover:bg-crimson-dark/20 dark:hover:bg-crimson/60"
                    >
                        Select From Your Device
                    </button>
                </div>
            </InputWrapper>
        </div>
    );
}
