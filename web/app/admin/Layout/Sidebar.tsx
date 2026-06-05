import React from 'react';
import Image from 'next/image';
import Link from 'next/link';
import SidebarCSR from './SidebarCSR';
import NavItem from './NavItem';
import NavItemIconRenderer from './NavItemIconRenderer';
import SidebarUserFooter from './SidebarUserFooter';
import GetUserInfo from '../../api/auth/GetUserInfo';
import QueueSidebarNotes from './Queue/QueueSidebarNotes';
import { authUrl } from '../../authUrl';

export default async function Sidebar (
    {
        activeNav = null,
    }: {
        activeNav: null | 'messages' | 'internalMessages' | 'profiles' | 'news' | 'menOfTheMark' | 'pastorsPage' | 'hymnsOfTheMonth' | 'resources' | 'mailingLists' | 'queue' | 'schedule';
    },
) {
    const userinfo = await GetUserInfo();

    const managePasswordUrl = authUrl('/manage-password');

    const navigation: NavItem[] = [];

    if (userinfo.roles.includes('EDIT_MESSAGES')) {
        navigation.push({
            name: 'Messages',
            href: '/admin/messages',
            icon: 'Microphone',
            current: activeNav === 'messages',
        });
    }

    if (userinfo.roles.includes('EDIT_MESSAGES')) {
        navigation.push({
            name: 'Internal Messages',
            href: '/admin/internal-messages',
            icon: 'LockClosed',
            current: activeNav === 'internalMessages',
        });
    }

    if (userinfo.roles.includes('EDIT_PROFILES')) {
        navigation.push({
            name: 'Profiles',
            href: '/admin/profiles',
            icon: 'Users',
            current: activeNav === 'profiles',
        });
    }

    if (userinfo.roles.includes('EDIT_NEWS')) {
        navigation.push({
            name: 'News',
            href: '/admin/news',
            icon: 'DocumentDuplicate',
            current: activeNav === 'news',
        });
    }

    if (userinfo.roles.includes('EDIT_MEN_OF_THE_MARK')) {
        navigation.push({
            name: 'Men of the Mark',
            href: '/admin/men-of-the-mark',
            icon: 'Newspaper',
            current: activeNav === 'menOfTheMark',
        });
    }

    if (userinfo.roles.includes('EDIT_PASTORS_PAGE')) {
        navigation.push({
            name: "Pastor's Page",
            href: '/admin/pastors-page',
            icon: 'BookOpen',
            current: activeNav === 'pastorsPage',
        });
    }

    if (userinfo.roles.includes('EDIT_HYMNS_OF_THE_MONTH')) {
        navigation.push({
            name: 'Hymns of the Month',
            href: '/admin/hymns-of-the-month',
            icon: 'MusicalNote',
            current: activeNav === 'hymnsOfTheMonth',
        });
    }

    if (userinfo.roles.includes('EDIT_RESOURCES')) {
        navigation.push({
            name: 'Resources',
            href: '/admin/resources',
            icon: 'DocumentArrowDown',
            current: activeNav === 'resources',
        });
    }

    if (userinfo.roles.includes('EDIT_MAILING_LISTS')) {
        navigation.push({
            name: 'Mailing Lists',
            href: '/admin/mailing-lists',
            icon: 'Envelope',
            current: activeNav === 'mailingLists',
        });
    }

    navigation.push({
        name: 'Schedule',
        href: '/admin/schedule',
        icon: 'Calendar',
        current: activeNav === 'schedule',
    });

    navigation.push({
        name: 'Queue',
        href: '/admin/queue',
        icon: 'QueueList',
        current: activeNav === 'queue',
    });

    return (
        <>
            <SidebarCSR
                navigation={navigation}
                loggedInAs={userinfo.email}
                managePasswordUrl={managePasswordUrl}
            />
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
                                                        {(() => {
                                                            if (item.name !== 'Queue') {
                                                                return null;
                                                            }

                                                            return (
                                                                <QueueSidebarNotes />
                                                            );
                                                        })()}
                                                    </Link>
                                                </li>
                                            ))}
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <SidebarUserFooter
                                loggedInAs={userinfo.email}
                                managePasswordUrl={managePasswordUrl}
                            />
                        </ul>
                    </nav>
                </div>
            </div>
        </>
    );
}
