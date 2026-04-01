import React from 'react';
import Link from 'next/link';
import { useFormStatus } from 'react-dom';
import { CheckIcon } from '@heroicons/react/16/solid';

export default function FormButtons (
    {
        submitButtonContent = 'Submit',
        submitButtonContentWhenPending = 'Submitting…',
        secondaryLinkContent = 'Cancel',
        secondaryLinkHref = undefined,
        location = 'bottom',
        isPending = undefined,
    }: {
        submitButtonContent?: string;
        submitButtonContentWhenPending?: string;
        secondaryLinkContent?: string;
        secondaryLinkHref?: string;
        location?: 'top' | 'bottom';
        isPending?: boolean;
    },
) {
    const { pending } = useFormStatus();

    const definitiveIsPending = isPending !== undefined ? isPending : pending;

    return (
        <div
            className={(() => {
                const classes = ['mt-6 flex items-center justify-end gap-x-3 col-span-full border-gray-200 dark:border-gray-500'];

                if (location === 'top') {
                    classes.push('border-b-2 pb-6');
                } else if (location === 'bottom') {
                    classes.push('border-t-2 pt-6');
                }

                return classes.join(' ');
            })()}
        >
            {(() => {
                if (!secondaryLinkHref) {
                    return null;
                }

                return (
                    <Link
                        href={secondaryLinkHref}
                        className="cursor-pointer rounded-md px-3 py-2 text-sm font-semibold shadow-xs bg-white text-gray-900 inset-ring inset-ring-gray-300 hover:bg-gray-50 dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20"
                    >
                        {secondaryLinkContent}
                    </Link>
                );
            })()}
            <button
                type="submit"
                disabled={definitiveIsPending}
                className={(() => {
                    const classes = ['inline-flex items-center rounded-md px-3 py-2 text-sm font-semibold shadow-xs dark:shadow-none select-none'];

                    if (definitiveIsPending) {
                        classes.push('cursor-default bg-gray-300 text-gray-500');
                    } else {
                        classes.push('cursor-pointer bg-crimson hover:bg-crimson-dark dark:bg-crimson/70 dark:hover:bg-crimson/80 text-white');
                    }

                    return classes.join(' ');
                })()}
            >
                <CheckIcon className="size-5 mr-1 -ml-1" aria-hidden="true" />
                {(() => {
                    if (definitiveIsPending) {
                        return submitButtonContentWhenPending;
                    }

                    return submitButtonContent;
                })()}
            </button>
        </div>
    );
}
