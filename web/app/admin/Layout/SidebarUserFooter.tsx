import React from 'react';

export default function SidebarUserFooter (
    {
        loggedInAs,
        managePasswordUrl,
    }: {
        loggedInAs: string;
        managePasswordUrl: string;
    },
) {
    return (
        <li className="-mx-6 mt-auto">
            <span className="flex items-center gap-x-2 px-6 py-3 text-sm/6 text-gray-900 dark:text-white">
                Logged in as <span className="font-semibold text-gray-700 dark:text-gray-100">{loggedInAs}</span>
            </span>
            <span className="flex items-center gap-x-2 px-6 pb-3 text-sm/6 text-gray-900 dark:text-white">
                <a
                    href={managePasswordUrl}
                    className="rounded-sm bg-crimson/30 px-2 py-1 text-xs font-semibold text-black dark:text-gray-200 shadow-xs hover:bg-crimson/40 focus-visible:outline-2 focus-visible:outline-offset-2 dark:shadow-none cursor-pointer"
                >
                    Manage Password
                </a>
                <a
                    href="/api/auth/sign-out"
                    className="rounded-sm bg-crimson/30 px-2 py-1 text-xs font-semibold text-black dark:text-gray-200 shadow-xs hover:bg-crimson/40 focus-visible:outline-2 focus-visible:outline-offset-2 dark:shadow-none cursor-pointer"
                >
                    Log Out
                </a>
            </span>
        </li>
    );
}
