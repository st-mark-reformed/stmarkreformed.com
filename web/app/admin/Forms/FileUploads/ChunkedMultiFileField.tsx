'use client';

import React, { useEffect, useRef, useState } from 'react';
import { TrashIcon } from '@heroicons/react/24/outline';
import { UploadAcceptType, uploadFileResumably } from './chunkedUpload';

export type MultiFileMetaField =
    | { kind: 'editableTitle'; name: string; label: string }
    | { kind: 'fileName'; name: string };

export interface MultiFileInitialRow {
    title: string;
    filename: string;
    // Existing stored relative path, or '' for an unfilled row.
    file: string;
}

interface Row {
    id: string;
    title: string;
    filename: string;
    // 'upload:{uploadId}' for a new file, an existing stored path, or ''.
    file: string;
    fileLabel: string;
    uploading: boolean;
    progress: number;
    error: string | undefined;
}

function labelForStoredFile (file: string): string {
    if (file === '') {
        return '';
    }

    return file.split('/').pop() ?? file;
}

export default function ChunkedMultiFileField (
    {
        heading,
        addLabel,
        removeAriaLabel,
        fileFieldName,
        fileAccept = undefined,
        fileLabel = undefined,
        acceptType,
        metaField,
        initialRows,
        onUploadingChange = undefined,
    }: {
        heading: string;
        addLabel: string;
        removeAriaLabel: string;
        fileFieldName: string;
        fileAccept?: string | undefined;
        fileLabel?: string | undefined;
        acceptType: UploadAcceptType;
        metaField: MultiFileMetaField;
        initialRows: Array<MultiFileInitialRow>;
        onUploadingChange?: ((isUploading: boolean) => void) | undefined;
    },
) {
    const nextId = useRef(0);
    const controllers = useRef<Record<string, AbortController>>({});

    const makeId = () => {
        nextId.current += 1;

        return `${fileFieldName}-${nextId.current}`;
    };

    const [rows, setRows] = useState<Array<Row>>(() => initialRows.map((row) => ({
        id: makeId(),
        title: row.title,
        filename: row.filename,
        file: row.file,
        fileLabel: metaField.kind === 'fileName'
            ? row.filename
            : labelForStoredFile(row.file),
        uploading: false,
        progress: 0,
        error: undefined,
    })));

    const isUploading = rows.some((row) => row.uploading);

    useEffect(() => {
        onUploadingChange?.(isUploading);
    }, [isUploading, onUploadingChange]);

    const updateRow = (id: string, changes: Partial<Row>) => {
        setRows((prev) => prev.map(
            (row) => (row.id === id ? { ...row, ...changes } : row),
        ));
    };

    const addRow = () => {
        setRows((prev) => [
            ...prev,
            {
                id: makeId(),
                title: '',
                filename: '',
                file: '',
                fileLabel: '',
                uploading: false,
                progress: 0,
                error: undefined,
            },
        ]);
    };

    const removeRow = (id: string) => {
        controllers.current[id]?.abort();
        delete controllers.current[id];

        setRows((prev) => prev.filter((row) => row.id !== id));
    };

    const handleFile = async (id: string, file: File | undefined) => {
        if (!file) {
            return;
        }

        controllers.current[id]?.abort();
        const controller = new AbortController();
        controllers.current[id] = controller;

        updateRow(id, { uploading: true, progress: 0, error: undefined });

        try {
            const uploadId = await uploadFileResumably(file, {
                acceptType,
                resumeKey: `${fileFieldName}:${id}:${file.name}:${file.size}`,
                onProgress: (fraction) => updateRow(id, { progress: fraction }),
                signal: controller.signal,
            });

            updateRow(id, {
                file: `upload:${uploadId}`,
                filename: file.name,
                fileLabel: file.name,
                uploading: false,
            });
        } catch (uploadException) {
            if (controller.signal.aborted) {
                return;
            }

            updateRow(id, {
                uploading: false,
                error: uploadException instanceof Error
                    ? uploadException.message
                    : 'The upload failed. Please try again.',
            });
        }
    };

    return (
        <div className="col-span-full">
            <span className="block text-sm/6 font-medium text-gray-900 dark:text-white">
                {heading}
            </span>
            <div className="mt-2 flex flex-col gap-4">
                {rows.map((row) => (
                    <div
                        key={row.id}
                        className="rounded-md border border-gray-300 bg-white p-4 dark:border-white/10 dark:bg-white/5"
                    >
                        <input
                            type="hidden"
                            name={fileFieldName}
                            value={row.file}
                        />
                        {metaField.kind === 'fileName'
                            ? (
                                <input
                                    type="hidden"
                                    name={metaField.name}
                                    value={row.filename}
                                />
                            )
                            : null}
                        <div className="flex flex-col gap-3 sm:flex-row sm:items-end">
                            {metaField.kind === 'editableTitle'
                                ? (
                                    <label className="flex-1">
                                        <span className="block text-sm/6 font-medium text-gray-900 dark:text-white">
                                            {metaField.label}
                                        </span>
                                        <input
                                            type="text"
                                            name={metaField.name}
                                            value={row.title}
                                            onChange={(event) => updateRow(row.id, { title: event.target.value })}
                                            className="mt-1 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 border-0 ring-1 ring-inset ring-gray-300 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-crimson-dark/50 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:ring-white/10"
                                        />
                                    </label>
                                )
                                : null}
                            <div className="flex-1">
                                <span className="block text-sm/6 font-medium text-gray-900 dark:text-white">
                                    {fileLabel ?? 'File'}
                                </span>
                                <input
                                    type="file"
                                    accept={fileAccept}
                                    onChange={(event) => {
                                        handleFile(row.id, event.target.files?.[0])
                                            .catch(() => {});
                                    }}
                                    className="mt-1 block w-full text-sm text-gray-600 cursor-pointer file:mr-3 file:cursor-pointer file:rounded-md file:border-0 file:bg-crimson/10 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-crimson hover:file:bg-crimson/20 dark:text-gray-300"
                                />
                                {(() => {
                                    if (row.uploading) {
                                        return (
                                            <span className="mt-1 block text-xs italic text-gray-500 dark:text-gray-400">
                                                Uploading… {Math.round(row.progress * 100)}%
                                            </span>
                                        );
                                    }

                                    if (row.error !== undefined) {
                                        return (
                                            <span className="mt-1 block text-xs text-crimson">
                                                {row.error}
                                            </span>
                                        );
                                    }

                                    if (row.fileLabel === '') {
                                        return null;
                                    }

                                    return (
                                        <span className="mt-1 block text-xs text-gray-500 dark:text-gray-400">
                                            Current: {row.fileLabel}
                                        </span>
                                    );
                                })()}
                            </div>
                            <button
                                type="button"
                                aria-label={removeAriaLabel}
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
                {addLabel}
            </button>
        </div>
    );
}
