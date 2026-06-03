'use client';

import React, { useState } from 'react';
import InputWrapper from '../Forms/InputWrapper';

function slugify (input: string): string {
    return input
        .normalize('NFKD')
        .replace(/\p{Diacritic}/gu, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

function inputClassName (error: string | undefined): string {
    const classes = ['block w-full rounded-md px-3 py-1.5 text-base text-gray-900 appearance-none border-0 outline-none ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-crimson-dark/50 sm:text-sm/6 dark:text-white dark:ring-white/10 dark:placeholder:text-gray-500'];

    if (error) {
        classes.push('bg-crimson/20 dark:bg-crimson/40');
    } else {
        classes.push('bg-white dark:bg-white/5');
    }

    return classes.join(' ');
}

/**
 * Title and Slug fields with slug auto-fill. The slug mirrors the title until
 * the editor types into the slug field; once edited, it stops auto-filling.
 * Clearing the slug re-enables auto-fill (and the API regenerates it from the
 * title if submitted empty).
 */
export default function NewsTitleSlugFields (
    {
        initialTitle,
        initialSlug,
        titleError,
        slugError,
    }: {
        initialTitle: string;
        initialSlug: string;
        titleError: string | undefined;
        slugError: string | undefined;
    },
) {
    const [title, setTitle] = useState(initialTitle);
    const [slug, setSlug] = useState(initialSlug);
    const [slugLocked, setSlugLocked] = useState(initialSlug !== '');

    return (
        <>
            <InputWrapper label="Title" name="title" error={titleError}>
                <input
                    id="title"
                    name="title"
                    type="text"
                    className={inputClassName(titleError)}
                    value={title}
                    onChange={(event) => {
                        const { value } = event.target;

                        setTitle(value);

                        if (!slugLocked) {
                            setSlug(slugify(value));
                        }
                    }}
                />
            </InputWrapper>
            <InputWrapper label="Slug" name="slug" error={slugError}>
                <input
                    id="slug"
                    name="slug"
                    type="text"
                    className={inputClassName(slugError)}
                    value={slug}
                    onChange={(event) => {
                        const { value } = event.target;

                        setSlug(value);
                        setSlugLocked(value !== '');
                    }}
                />
            </InputWrapper>
        </>
    );
}
