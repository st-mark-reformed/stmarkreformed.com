import React from 'react';
import Image from 'next/image';
import Link from 'next/link';
import SidebarCSR from './SidebarCSR';
import NavItem from './NavItem';
import NavItemIconRenderer from './NavItemIconRenderer';

export default async function Sidebar (
    {
        activeNav = null,
    }: {
        activeNav: null | 'messages' | 'profiles';
    },
) {
    const navigation: NavItem[] = [
        {
            name: 'Messages',
            href: '/admin/messages',
            icon: 'Microphone',
            current: activeNav === 'messages',
        },
        {
            name: 'Profiles',
            href: '/admin/profiles',
            icon: 'Users',
            current: activeNav === 'profiles',
        },
    ];

    return (
        <>
            <SidebarCSR navigation={navigation} />
            <div className="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col dark:bg-gray-800">
                <div className="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6 dark:border-white/10 dark:bg-black/10">
                    <div className="flex h-16 shrink-0 items-center">
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
                    <nav className="flex flex-1 flex-col">
                        <ul className="flex flex-1 flex-col gap-y-7">
                            <li>
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
                            </li>
                            <li className="-mx-6 mt-auto">
                                <span className="flex items-center gap-x-2 px-6 py-3 text-sm/6 text-gray-900 dark:text-white">
                                    Logged in as <span className="font-semibold text-gray-700 dark:text-gray-100">Tom Cook</span>
                                </span>
                                <span className="flex items-center gap-x-2 px-6 pb-3 text-sm/6 text-gray-900 dark:text-white">
                                    <button
                                        type="button"
                                        className="rounded-sm bg-crimson/30 px-2 py-1 text-xs font-semibold text-black dark:text-gray-200 shadow-xs hover:bg-crimson/40 focus-visible:outline-2 focus-visible:outline-offset-2 dark:shadow-none cursor-pointer"
                                    >
                                        Log Out
                                    </button>
                                </span>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </>
    );
}
