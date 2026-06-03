'use client';

import React, { useState } from 'react';
import { useRouter } from 'next/navigation';
import { MagnifyingGlassIcon } from '@heroicons/react/24/solid';

export default function NewsSearchForm (
    {
        defaultKeyword,
    }: {
        defaultKeyword: string;
    },
) {
    const router = useRouter();

    const [keyword, setKeyword] = useState(defaultKeyword);

    const submit = (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        const trimmed = keyword.trim();

        if (trimmed === '') {
            router.push('/admin/news');

            return;
        }

        router.push(`/admin/news?keyword=${encodeURIComponent(trimmed)}`);
    };

    return (
        <form onSubmit={submit} className="flex w-full items-center gap-2 sm:w-auto">
            <input
                type="search"
                name="keyword"
                aria-label="Search news"
                placeholder="Search news…"
                value={keyword}
                onChange={(event) => setKeyword(event.target.value)}
                className="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 appearance-none border-0 outline-none ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-crimson-dark/50 sm:w-64 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:ring-white/10 dark:placeholder:text-gray-500"
            />
            <button
                type="submit"
                aria-label="Search"
                className="inline-flex shrink-0 items-center rounded-md bg-crimson px-3 py-2 text-sm font-semibold text-white shadow-xs select-none cursor-pointer hover:bg-crimson-dark dark:bg-crimson/70 dark:shadow-none dark:hover:bg-crimson/80"
            >
                <MagnifyingGlassIcon className="size-5" aria-hidden="true" />
            </button>
        </form>
    );
}
