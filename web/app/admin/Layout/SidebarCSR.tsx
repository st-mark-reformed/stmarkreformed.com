'use client';

import React, { useState } from 'react';
import {
    Dialog,
    DialogBackdrop,
    DialogPanel,
    TransitionChild,
} from '@headlessui/react';
import {
    Bars3Icon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import Image from 'next/image';
import Link from 'next/link';
import NavItem from './NavItem';
import NavItemIconRenderer from './NavItemIconRenderer';

export default function SidebarCSR (
    {
        navigation,
        loggedInAs,
    }: {
        navigation: NavItem[];
        loggedInAs: string;
    },
) {
    const [sidebarOpen, setSidebarOpen] = useState(false);

    return (
        <>
            <Dialog open={sidebarOpen} onClose={setSidebarOpen} className="relative z-50 lg:hidden">
                <DialogBackdrop
                    transition
                    className="fixed inset-0 bg-gray-900/80 transition-opacity duration-300 ease-linear data-closed:opacity-0"
                />

                <div className="fixed inset-0 flex">
                    <DialogPanel
                        transition
                        className="relative mr-16 flex w-full max-w-xs flex-1 transform transition duration-300 ease-in-out data-closed:-translate-x-full"
                    >
                        <TransitionChild>
                            <div className="absolute top-0 left-full flex w-16 justify-center pt-5 duration-300 ease-in-out data-closed:opacity-0">
                                <button type="button" onClick={() => setSidebarOpen(false)} className="-m-2.5 p-2.5 cursor-pointer">
                                    <span className="sr-only">Close sidebar</span>
                                    <XMarkIcon aria-hidden="true" className="size-6 text-white" />
                                </button>
                            </div>
                        </TransitionChild>
                        <div className="relative flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-2 dark:bg-gray-900 dark:ring dark:ring-white/10 dark:before:pointer-events-none dark:before:absolute dark:before:inset-0 dark:before:bg-black/10">
                            <div className="relative flex h-16 shrink-0 items-center">
                                <Image
                                    alt="St. Mark Reformed Church"
                                    src="/images/logo/logo-website-header.png"
                                    width={35}
                                    height={32}
                                />
                                <span className="font-semibold ml-3 mt-1 text-gray-900 dark:text-gray-50">
                                    SMRC Admin
                                </span>
                            </div>
                            <nav className="relative flex flex-1 flex-col">
                                <ul className="flex flex-1 flex-col gap-y-7">
                                    <li>
                                        <ul className="-mx-2 space-y-1">
                                            {navigation.map((item) => (
                                                <li key={item.name}>
                                                    <Link
                                                        href={item.href}
                                                        className={(() => {
                                                            const classes = ['group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold'];

                                                            if (item.current) {
                                                                classes.push('bg-crimson/30 text-gray-900 dark:text-gray-200 cursor-default');
                                                            } else {
                                                                classes.push('text-gray-700 dark:text-gray-300 hover:bg-crimson/30 dark:hover:text-white');
                                                            }

                                                            return classes.join(' ');
                                                        })()}
                                                    >
                                                        <NavItemIconRenderer item={item} />
                                                        {item.name}
                                                    </Link>
                                                </li>
                                            ))}
                                        </ul>
                                    </li>

                                </ul>
                            </nav>
                        </div>
                    </DialogPanel>
                </div>
            </Dialog>

            <div className="sticky top-0 z-40 flex items-center gap-x-6 bg-white px-4 py-4 shadow-xs sm:px-6 lg:hidden dark:bg-gray-900 dark:shadow-none dark:after:pointer-events-none dark:after:absolute dark:after:inset-0 dark:after:border-b dark:after:border-white/10 dark:after:bg-black/10">
                <button
                    type="button"
                    onClick={() => setSidebarOpen(true)}
                    className="-m-2.5 p-2.5 text-gray-700 hover:text-gray-900 lg:hidden dark:text-gray-400 dark:hover:text-white cursor-pointer"
                >
                    <span className="sr-only">Open sidebar</span>
                    <Bars3Icon aria-hidden="true" className="size-6" />
                </button>
                <span className="flex ml-auto items-center gap-x-2 pl-6 py-3 text-sm/6 text-gray-900 dark:text-white">
                    Logged in as <span className="font-semibold text-gray-700 dark:text-gray-100">{loggedInAs}</span>
                    <a
                        href="/api/auth/sign-out"
                        className="rounded-sm bg-crimson/30 px-2 py-1 text-xs font-semibold text-black dark:text-gray-200 shadow-xs hover:bg-crimson/40 focus-visible:outline-2 focus-visible:outline-offset-2 dark:shadow-none cursor-pointer"
                    >
                        Log Out
                    </a>
                </span>
            </div>
        </>
    );
}
