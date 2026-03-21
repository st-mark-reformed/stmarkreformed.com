import React from 'react';
import { ChevronRightIcon } from '@heroicons/react/20/solid';
import PageTitle from '../PageTitle';
import RequestFactory from '../../api/request/RequestFactory';
import Breadcrumbs from '../Breadcrumbs';

const people = [
    {
        name: 'Leslie Alexander',
        email: 'leslie.alexander@example.com',
        role: 'Co-Founder / CEO',
        href: '#',
        lastSeen: '3h ago',
        lastSeenDateTime: '2023-01-23T13:23Z',
    },
    {
        name: 'Michael Foster',
        email: 'michael.foster@example.com',
        role: 'Co-Founder / CTO',
        href: '#',
        lastSeen: '3h ago',
        lastSeenDateTime: '2023-01-23T13:23Z',
    },
    {
        name: 'Dries Vincent',
        email: 'dries.vincent@example.com',
        role: 'Business Relations',
        href: '#',
        lastSeen: null,
    },
    {
        name: 'Lindsay Walton',
        email: 'lindsay.walton@example.com',
        role: 'Front-end Developer',
        href: '#',
        lastSeen: '3h ago',
        lastSeenDateTime: '2023-01-23T13:23Z',
    },
    {
        name: 'Courtney Henry',
        email: 'courtney.henry@example.com',
        role: 'Designer',
        href: '#',
        lastSeen: '3h ago',
        lastSeenDateTime: '2023-01-23T13:23Z',
    },
    {
        name: 'Tom Cook',
        email: 'tom.cook@example.com',
        role: 'Director of Product',
        href: '#',
        lastSeen: null,
    },
];

export default async function ProfilesPage () {
    const tmp = await RequestFactory().makeWithSignInRedirect({
        uri: '/admin/profiles',
        cacheSeconds: 0,
    });

    console.log(tmp);

    return (
        <>
            <Breadcrumbs />
            <PageTitle
                buttons={[
                    {
                        type: 'primary',
                        content: 'New Profile',
                        glyph: 'plus',
                        href: '/admin/profiles/new',
                    },
                ]}
            >
                Profiles
            </PageTitle>
            <ul className="divide-y divide-gray-100 overflow-hidden bg-white shadow-xs outline-1 outline-gray-900/5 sm:rounded-xl dark:divide-white/5 dark:bg-gray-800/50 dark:shadow-none dark:outline-white/10 dark:sm:-outline-offset-1">
                {people.map((person) => (
                    <li
                        key={person.email}
                        className="relative flex justify-between gap-x-6 px-4 py-5 hover:bg-gray-50 sm:px-6 dark:hover:bg-white/2.5"
                    >
                        <div className="flex min-w-0 gap-x-4">
                            <div className="min-w-0 flex-auto">
                                <p className="text-sm/6 font-semibold text-gray-900 dark:text-white">
                                    <a href={person.href}>
                                        <span className="absolute inset-x-0 -top-px bottom-0" />
                                        {person.name}
                                    </a>
                                </p>
                                <p className="mt-1 flex text-xs/5 text-gray-500 dark:text-gray-400">
                                    <a href={`mailto:${person.email}`} className="relative truncate hover:underline">
                                        {person.email}
                                    </a>
                                </p>
                            </div>
                        </div>
                        <div className="flex shrink-0 items-center gap-x-4">
                            <div className="hidden sm:flex sm:flex-col sm:items-end">
                                <p className="text-sm/6 text-gray-900 dark:text-white">{person.role}</p>
                                {person.lastSeen ? (
                                    <p className="mt-1 text-xs/5 text-gray-500 dark:text-gray-400">
                                        Last seen <time dateTime={person.lastSeenDateTime}>{person.lastSeen}</time>
                                    </p>
                                ) : (
                                    <div className="mt-1 flex items-center gap-x-1.5">
                                        <div className="flex-none rounded-full bg-emerald-500/20 p-1 dark:bg-emerald-500/30">
                                            <div className="size-1.5 rounded-full bg-emerald-500" />
                                        </div>
                                        <p className="text-xs/5 text-gray-500 dark:text-gray-400">Online</p>
                                    </div>
                                )}
                            </div>
                            <ChevronRightIcon aria-hidden="true" className="size-5 flex-none text-gray-400 dark:text-gray-500" />
                        </div>
                    </li>
                ))}
            </ul>
        </>
    );
}
