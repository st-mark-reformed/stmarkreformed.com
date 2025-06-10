import React, { Fragment } from 'react';
import { Cog6ToothIcon } from '@heroicons/react/24/outline';
import Link from 'next/link';

export default function SidebarDesktop (
    {
        navigation,
    }: {
        navigation: Array<NavigationItem>;
    },
) {
    return (
        <div className="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
            {/* Sidebar component, swap this element with another sidebar if you like */}
            <div className="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6 pb-4">
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
                                                                    classes.push('bg-gray-100 text-cyan-600');
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
                                                                classes.push('bg-gray-100 text-cyan-600');
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
        </div>
    );
}
