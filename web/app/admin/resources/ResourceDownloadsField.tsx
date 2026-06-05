'use client';

import React, { useRef, useState } from 'react';
import { TrashIcon } from '@heroicons/react/24/outline';
import { CreateEditResourceDownloadValue } from './CreateEditResourceValues';

type Row = {
    id: string;
    // The stored/original filename.
    filename: string;
    // Existing stored file (empty) or a new base64 data URI.
    file: string;
};

function fileToDataUrl (file: File): Promise<string> {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();

        reader.onload = () => {
            const { result } = reader;

            if (typeof result !== 'string') {
                reject(new Error('Failed to read file'));

                return;
            }

            resolve(result);
        };

        reader.onerror = () => reject(new Error('Failed to read file'));
        reader.readAsDataURL(file);
    });
}

/**
 * Repeatable download rows. Each row is a single file; the filename shown to the
 * editor (and embedded in the public URL) is the original upload name. Existing
 * downloads carry an empty `file` and are kept by `filename`; newly chosen files
 * carry a base64 data URI the API writes to disk.
 */
export default function ResourceDownloadsField (
    {
        initialDownloads,
    }: {
        initialDownloads: CreateEditResourceDownloadValue[];
    },
) {
    const nextId = useRef(0);

    const makeId = () => {
        nextId.current += 1;

        return `download-${nextId.current}`;
    };

    const [rows, setRows] = useState<Row[]>(
        () => initialDownloads.map((download) => ({
            id: makeId(),
            filename: download.filename,
            file: download.file,
        })),
    );

    const updateRow = (id: string, changes: Partial<Row>) => {
        setRows((prev) => prev.map(
            (row) => (row.id === id ? { ...row, ...changes } : row),
        ));
    };

    const addRow = () => {
        setRows((prev) => [
            ...prev,
            { id: makeId(), filename: '', file: '' },
        ]);
    };

    const removeRow = (id: string) => {
        setRows((prev) => prev.filter((row) => row.id !== id));
    };

    const handleFile = async (id: string, file: File | undefined) => {
        if (!file) {
            return;
        }

        const dataUrl = await fileToDataUrl(file);

        updateRow(id, { file: dataUrl, filename: file.name });
    };

    return (
        <div className="col-span-full">
            <span className="block text-sm/6 font-medium text-gray-900 dark:text-white">
                Downloads
            </span>
            <div className="mt-2 flex flex-col gap-4">
                {rows.map((row) => (
                    <div
                        key={row.id}
                        className="rounded-md border border-gray-300 bg-white p-4 dark:border-white/10 dark:bg-white/5"
                    >
                        <input
                            type="hidden"
                            name="downloadFilename"
                            value={row.filename}
                        />
                        <input
                            type="hidden"
                            name="downloadFile"
                            value={row.file}
                        />
                        <div className="flex flex-col gap-3 sm:flex-row sm:items-end">
                            <div className="flex-1">
                                <span className="block text-sm/6 font-medium text-gray-900 dark:text-white">
                                    File
                                </span>
                                <input
                                    type="file"
                                    onChange={(event) => {
                                        handleFile(row.id, event.target.files?.[0])
                                            .catch(() => {});
                                    }}
                                    className="mt-1 block w-full text-sm text-gray-600 cursor-pointer file:mr-3 file:cursor-pointer file:rounded-md file:border-0 file:bg-crimson/10 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-crimson hover:file:bg-crimson/20 dark:text-gray-300"
                                />
                                {(() => {
                                    if (row.filename === '') {
                                        return null;
                                    }

                                    return (
                                        <span className="mt-1 block text-xs text-gray-500 dark:text-gray-400">
                                            Current: {row.filename}
                                        </span>
                                    );
                                })()}
                            </div>
                            <button
                                type="button"
                                aria-label="Remove download"
                                onClick={() => removeRow(row.id)}
                                className="inline-flex shrink-0 cursor-pointer items-center justify-center rounded-md bg-crimson/10 px-3 py-2 text-sm font-semibold text-crimson hover:bg-crimson/20 dark:bg-crimson/40 dark:text-white"
                            >
                                <TrashIcon className="size-5" aria-hidden="true" />
                            </button>
                        </div>
                    </div>
                ))}
            </div>
            <button
                type="button"
                onClick={addRow}
                className="mt-3 inline-flex cursor-pointer items-center rounded-md bg-crimson px-3 py-2 text-sm font-semibold text-white hover:bg-crimson-dark dark:bg-crimson/70 dark:hover:bg-crimson/80"
            >
                Add Download
            </button>
        </div>
    );
}
