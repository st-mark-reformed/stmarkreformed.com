import React, { useEffect, useState } from 'react';
import { FileUploader } from 'react-drag-drop-files';
import { ImageUploadFileTypes } from './ImageUploadFileTypes';
import Message from '../messaging/Message';
import DndFileDisplay from './DndFileDisplay';
import useApiFeUrl from '../../useApiUrl';

interface FileInfo {
    name: string;
    data: string;
}

export default function DndSingleFileUploader (
    {
        name,
        fileUrl,
        fileTypes = ImageUploadFileTypes,
        handleChange,
        setFileData,
        maxSizeMb = 80,
    }: {
        name: string;
        fileUrl: string;
        fileTypes?: Array<string>;
        handleChange?: (file: File) => void;
        setFileData?: (data: string) => void;
        maxSizeMb?: number;
    },
) {
    const apiFeUrl = useApiFeUrl();

    const [errorMsg, setErrorMsg] = useState('');

    const [errorMsgIsVisible, setErrorMsgIsVisible] = useState(
        false,
    );

    const [uploadedFile, setUploadedFile] = useState<File | null>(
        null,
    );

    useEffect(() => {
        if (!setFileData || !uploadedFile) {
            return;
        }

        const reader = new FileReader();

        reader.onload = (e) => {
            setFileData(JSON.stringify({
                name: uploadedFile.name,
                data: e.target?.result || '',
            } as FileInfo));
        };

        reader.readAsDataURL(uploadedFile);

        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [uploadedFile]);

    const errorHandler = (err: string) => {
        setErrorMsg(err);
        setErrorMsgIsVisible(true);
    };

    return (
        <div className="space-y-3">
            <Message
                type="error"
                isVisible={errorMsgIsVisible}
                setIsVisible={setErrorMsgIsVisible}
                heading={errorMsg}
            />
            <div>
                <FileUploader
                    handleChange={(file: File) => {
                        setErrorMsg('');

                        setErrorMsgIsVisible(false);

                        setUploadedFile(file);

                        if (handleChange) {
                            handleChange(file);
                        }
                    }}
                    name={name}
                    types={fileTypes}
                    maxSize={maxSizeMb}
                    onSizeError={errorHandler}
                    onTypeError={errorHandler}
                >
                    <div className="col-span-full">
                        <div className="flex justify-center rounded-lg border border-dashed border-gray-900/25 px-4 py-4">
                            <div className="text-center">
                                {(() => {
                                    if (fileUrl) {
                                        if (fileUrl.startsWith('{"name":')) {
                                            const fileInfo = JSON.parse(fileUrl) as FileInfo;

                                            let icon = 'generic-file';

                                            if (fileInfo.name.endsWith('.mp3')) {
                                                icon = 'audio-file';
                                            }

                                            const isImage = ImageUploadFileTypes.some(
                                                (ending) => fileInfo.name.toLowerCase().endsWith(ending.toLowerCase()),
                                            );

                                            if (isImage) {
                                                icon = fileInfo.data;
                                            }

                                            return (
                                                <DndFileDisplay
                                                    icon={icon}
                                                    text={fileInfo.name}
                                                />
                                            );
                                        }

                                        let icon = 'generic-file';

                                        if (fileUrl.endsWith('.mp3')) {
                                            icon = 'audio-file';
                                        }

                                        const isImage = ImageUploadFileTypes.some(
                                            (ending) => fileUrl.toLowerCase().endsWith(ending.toLowerCase()),
                                        );

                                        if (isImage) {
                                            if (!apiFeUrl) {
                                                return null;
                                            }

                                            icon = `${apiFeUrl}/${fileUrl}`;
                                        }

                                        return (
                                            <DndFileDisplay
                                                icon={icon}
                                                // @ts-expect-error TS2322
                                                text={fileUrl.split('/').at(-1)}
                                            />
                                        );
                                    }

                                    return null;
                                })()}
                                <div className="text-sm leading-6 text-gray-600">
                                    <label
                                        htmlFor="file-upload"
                                        className="relative cursor-pointer rounded-md bg-white font-semibold text-cyan-600 focus-within:outline-none focus-within:ring-2 hover:text-cyan-700"
                                    >
                                        <span>Upload a {fileUrl ? 'new' : ''} file</span>
                                    </label>
                                </div>
                                <p className="text-xs leading-5 text-gray-600">or drag and drop a {fileUrl ? 'new' : ''} file here</p>
                            </div>
                        </div>
                    </div>
                </FileUploader>
            </div>
        </div>
    );
}
