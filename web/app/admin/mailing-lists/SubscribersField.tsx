'use client';

import React, { useRef, useState } from 'react';
import { TrashIcon } from '@heroicons/react/24/outline';
import { CreateEditMailingListSubscriberValue } from './CreateEditMailingListValues';

type Row = {
    id: string;
    name: string;
    emailAddress: string;
};

/**
 * Repeatable subscriber rows. Each row contributes a `subscriberName` and
 * `subscriberEmail` input; the API zips them back together by position and
 * drops any row without an email address.
 */
export default function SubscribersField (
    {
        initialSubscribers,
    }: {
        initialSubscribers: CreateEditMailingListSubscriberValue[];
    },
) {
    const nextId = useRef(0);

    const makeId = () => {
        nextId.current += 1;

        return `subscriber-${nextId.current}`;
    };

    const [rows, setRows] = useState<Row[]>(
        () => initialSubscribers.map((subscriber) => ({
            id: makeId(),
            name: subscriber.name,
            emailAddress: subscriber.emailAddress,
        })),
    );

    const addRow = () => {
        setRows((prev) => [
            ...prev,
            { id: makeId(), name: '', emailAddress: '' },
        ]);
    };

    const removeRow = (id: string) => {
        setRows((prev) => prev.filter((row) => row.id !== id));
    };

    return (
        <div className="col-span-full">
            <span className="block text-sm/6 font-medium text-gray-900 dark:text-white">
                Subscribers
            </span>
            <div className="mt-2 flex flex-col gap-4">
                {rows.map((row) => (
                    <div
                        key={row.id}
                        className="rounded-md border border-gray-300 bg-white p-4 dark:border-white/10 dark:bg-white/5"
                    >
                        <div className="flex flex-col gap-3 sm:flex-row sm:items-end">
                            <div className="flex-1">
                                <span className="block text-sm/6 font-medium text-gray-900 dark:text-white">
                                    Name
                                </span>
                                <input
                                    type="text"
                                    name="subscriberName"
                                    defaultValue={row.name}
                                    autoComplete="off"
                                    className="mt-1 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 appearance-none border-0 outline-none ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-crimson-dark/50 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:ring-white/10 dark:placeholder:text-gray-500"
                                />
                            </div>
                            <div className="flex-1">
                                <span className="block text-sm/6 font-medium text-gray-900 dark:text-white">
                                    Email Address
                                </span>
                                <input
                                    type="email"
                                    name="subscriberEmail"
                                    defaultValue={row.emailAddress}
                                    autoComplete="off"
                                    className="mt-1 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 appearance-none border-0 outline-none ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-crimson-dark/50 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:ring-white/10 dark:placeholder:text-gray-500"
                                />
                            </div>
                            <button
                                type="button"
                                aria-label="Remove subscriber"
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
                Add Subscriber
            </button>
        </div>
    );
}
