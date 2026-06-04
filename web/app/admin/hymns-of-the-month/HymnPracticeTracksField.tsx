'use client';

import React, { useRef, useState } from 'react';
import { TrashIcon } from '@heroicons/react/24/outline';
import { CreateEditHymnOfTheMonthPracticeTrackValue } from './CreateEditHymnOfTheMonthValues';

type Row = {
    id: string;
    title: string;
    // Existing stored relative path or a new base64 data URI.
    file: string;
    // Human-readable label for the currently attached file.
    fileLabel: string;
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

function labelForFile (file: string): string {
    if (file === '') {
        return '';
    }

    if (file.startsWith('data:')) {
        return 'New file selected';
    }

    return file.split('/').pop() ?? file;
}

export default function HymnPracticeTracksField (
    {
        initialTracks,
    }: {
        initialTracks: CreateEditHymnOfTheMonthPracticeTrackValue[];
    },
) {
    const nextId = useRef(0);

    const makeId = () => {
        nextId.current += 1;

        return `track-${nextId.current}`;
    };

    const [rows, setRows] = useState<Row[]>(() => initialTracks.map((track) => ({
        id: makeId(),
        title: track.title,
        file: track.file,
        fileLabel: labelForFile(track.file),
    })));

    const updateRow = (id: string, changes: Partial<Row>) => {
        setRows((prev) => prev.map(
            (row) => (row.id === id ? { ...row, ...changes } : row),
        ));
    };

    const addRow = () => {
        setRows((prev) => [
            ...prev,
            {
                id: makeId(), title: '', file: '', fileLabel: '',
            },
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

        updateRow(id, { file: dataUrl, fileLabel: file.name });
    };

    return (
        <div className="col-span-full">
            <span className="block text-sm/6 font-medium text-gray-900 dark:text-white">
                Practice Tracks
            </span>
            <div className="mt-2 flex flex-col gap-4">
                {rows.map((row) => (
                    <div
                        key={row.id}
                        className="rounded-md border border-gray-300 bg-white p-4 dark:border-white/10 dark:bg-white/5"
                    >
                        <input
                            type="hidden"
                            name="practiceTrackFile"
                            value={row.file}
                        />
                        <div className="flex flex-col gap-3 sm:flex-row sm:items-end">
                            <label className="flex-1">
                                <span className="block text-sm/6 font-medium text-gray-900 dark:text-white">
                                    Title
                                </span>
                                <input
                                    type="text"
                                    name="practiceTrackTitle"
                                    value={row.title}
                                    onChange={(event) => updateRow(row.id, { title: event.target.value })}
                                    className="mt-1 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 border-0 ring-1 ring-inset ring-gray-300 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-crimson-dark/50 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:ring-white/10"
                                />
                            </label>
                            <div className="flex-1">
                                <span className="block text-sm/6 font-medium text-gray-900 dark:text-white">
                                    Audio file (MP3)
                                </span>
                                <input
                                    type="file"
                                    accept=".mp3"
                                    onChange={(event) => {
                                        handleFile(row.id, event.target.files?.[0])
                                            .catch(() => {});
                                    }}
                                    className="mt-1 block w-full text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-crimson/10 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-crimson hover:file:bg-crimson/20 dark:text-gray-300"
                                />
                                {(() => {
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
                                aria-label="Remove track"
                                onClick={() => removeRow(row.id)}
                                className="inline-flex shrink-0 items-center justify-center rounded-md bg-crimson/10 px-3 py-2 text-sm font-semibold text-crimson hover:bg-crimson/20 dark:bg-crimson/40 dark:text-white"
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
                className="mt-3 inline-flex items-center rounded-md bg-crimson px-3 py-2 text-sm font-semibold text-white hover:bg-crimson-dark dark:bg-crimson/70 dark:hover:bg-crimson/80"
            >
                Add Practice Track
            </button>
        </div>
    );
}
