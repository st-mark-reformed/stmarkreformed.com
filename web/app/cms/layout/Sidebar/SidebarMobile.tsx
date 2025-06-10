import React, { Dispatch, Fragment, SetStateAction } from 'react';
import {
    Dialog, DialogPanel, Transition, TransitionChild,
} from '@headlessui/react';
import { Cog6ToothIcon, XMarkIcon } from '@heroicons/react/24/outline';
import Link from 'next/link';

export default function SidebarMobile (
    {
        sidebarOpen,
        setSidebarOpen,
        navigation,
    }: {
        sidebarOpen: boolean;
        setSidebarOpen: Dispatch<SetStateAction<boolean>>;
        navigation: Array<NavigationItem>;
    },
) {
    return (
        <Transition show={sidebarOpen} as={Fragment}>
            <Dialog as="div" className="relative z-50 lg:hidden" onClose={setSidebarOpen}>
                <TransitionChild
                    as={Fragment}
                    enter="transition-opacity ease-linear duration-300"
                    enterFrom="opacity-0"
                    enterTo="opacity-100"
                    leave="transition-opacity ease-linear duration-300"
                    leaveFrom="opacity-100"
                    leaveTo="opacity-0"
                >
                    <div className="fixed inset-0 bg-gray-900/80" />
                </TransitionChild>
                <div className="fixed inset-0 flex">
                    <TransitionChild
                        as={Fragment}
                        enter="transition ease-in-out duration-300 transform"
                        enterFrom="-translate-x-full"
                        enterTo="translate-x-0"
                        leave="transition ease-in-out duration-300 transform"
                        leaveFrom="translate-x-0"
                        leaveTo="-translate-x-full"
                    >
                        <DialogPanel className="relative mr-16 flex w-full max-w-xs flex-1">
                            <TransitionChild
                                as={Fragment}
                                enter="ease-in-out duration-300"
                                enterFrom="opacity-0"
                                enterTo="opacity-100"
                                leave="ease-in-out duration-300"
                                leaveFrom="opacity-100"
                                leaveTo="opacity-0"
                            >
                                <div className="absolute left-full top-0 flex w-16 justify-center pt-5">
                                    <button
                                        type="button"
                                        className="-m-2.5 p-2.5 cursor-pointer"
                                        onClick={() => setSidebarOpen(false)}
                                    >
                                        <span className="sr-only">Close sidebar</span>
                                        <XMarkIcon className="h-6 w-6 text-white" aria-hidden="true" />
                                    </button>
                                </div>
                            </TransitionChild>
                            <div className="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-4">
                                <div className="flex h-16 shrink-0 items-center">
                                    <img
                                        className="h-8 w-auto"
                                        src="/images/logo/logo-website-header.png"
                                        alt="SMRC CMS"
                                    />
                                    <span className="ml-3 font-bold">SMRC CMS</span>
                                </div>
                                <nav className="flex flex-1 flex-col">
                                    <ul className="flex flex-1 flex-col gap-y-7">
                                        <li>
                                            <ul className="-mx-2 space-y-1">
                                                {navigation.map((item) => (
                                                    <Fragment key={item.name}>
                                                        <li>
                                                            {(() => {
                                                                if (item.href) {
                                                                    return (
                                                                        <Link
                                                                            href={item.href}
                                                                            className={(() => {
                                                                                const classes = ['group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold'];

                                                                                if (item.current) {
                                                                                    classes.push('bg-gray-50 text-cyan-600');
                                                                                } else {
                                                                                    classes.push('text-gray-700 hover:text-cyan-600 hover:bg-gray-50');
                                                                                }

                                                                                return classes.join(' ');
                                                                            })()}
                                                                        >
                                                                            <item.icon
                                                                                className={(() => {
                                                                                    const classes = ['h-6 w-6 shrink-0'];

                                                                                    if (item.current) {
                                                                                        classes.push('text-cyan-600');
                                                                                    } else {
                                                                                        classes.push('text-gray-400 group-hover:text-cyan-600');
                                                                                    }

                                                                                    return classes.join(' ');
                                                                                })()}
                                                                                aria-hidden="true"
                                                                            />
                                                                            {item.name}
                                                                        </Link>
                                                                    );
                                                                }

                                                                return (
                                                                    <span
                                                                        className={(() => {
                                                                            const classes = ['group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold select-none'];

                                                                            if (item.current) {
                                                                                classes.push('bg-gray-50 text-cyan-600');
                                                                            } else {
                                                                                classes.push('text-gray-700');
                                                                            }

                                                                            return classes.join(' ');
                                                                        })()}
                                                                    >
                                                                        <item.icon
                                                                            className={(() => {
                                                                                const classes = ['h-6 w-6 shrink-0'];

                                                                                if (item.current) {
                                                                                    classes.push('text-cyan-600');
                                                                                } else {
                                                                                    classes.push('text-gray-400');
                                                                                }

                                                                                return classes.join(' ');
                                                                            })()}
                                                                            aria-hidden="true"
                                                                        />
                                                                        {item.name}
                                                                    </span>
                                                                );
                                                            })()}
                                                        </li>
                                                    </Fragment>
                                                ))}
                                            </ul>
                                        </li>
                                        <li className="mt-auto">
                                            <Link
                                                href="/admin"
                                                className="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-700 hover:bg-gray-50 hover:text-cyan-600"
                                            >
                                                <Cog6ToothIcon
                                                    className="h-6 w-6 shrink-0 text-gray-400 group-hover:text-cyan-600"
                                                    aria-hidden="true"
                                                />
                                                Admin
                                            </Link>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </Dialog>
        </Transition>
    );
}
