import React from 'react';
import Link from 'next/link';

export default function FormButtons (
    {
        submitButtonContent = 'Submit',
        secondaryLinkContent = 'Cancel',
        secondaryLinkHref = undefined,
        location = 'bottom',
    }: {
        submitButtonContent?: string;
        secondaryLinkContent?: string;
        secondaryLinkHref?: string;
        location?: 'top' | 'bottom';
    },
) {
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
                className="cursor-pointer rounded-md bg-crimson px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-crimson-dark dark:bg-crimson/70 dark:shadow-none dark:hover:bg-crimson/80"
            >
                {submitButtonContent}
            </button>
        </div>
    );
}
