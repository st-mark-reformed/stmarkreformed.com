import React from 'react';
import { ChevronRightIcon } from '@heroicons/react/20/solid';
import PageTitle from '../PageTitle';
import Breadcrumbs from '../Breadcrumbs';
import GetProfiles from './GetProfiles';

export default async function ProfilesPage () {
    const profiles = await GetProfiles();

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
            {(() => {
                if (profiles.length === 0) {
                    return (
                        <div className="text-center">
                            <p className="text-sm/6 text-gray-500 dark:text-gray-400">
                                No profiles found.
                            </p>
                        </div>
                    );
                }

                return (
                    <ul className="divide-y divide-gray-100 overflow-hidden bg-white shadow-xs outline-1 outline-gray-900/5 sm:rounded-xl dark:divide-white/5 dark:bg-gray-800/50 dark:shadow-none dark:outline-white/10 dark:sm:-outline-offset-1">
                        {profiles.map((profile) => (
                            <li
                                key={profile.id}
                                className="relative flex justify-between gap-x-6 px-4 py-5 hover:bg-gray-50 sm:px-6 dark:hover:bg-white/2.5"
                            >
                                <div className="flex min-w-0 gap-x-4">
                                    <div className="min-w-0 flex-auto">
                                        <p className="text-sm/6 font-semibold text-gray-900 dark:text-white">
                                            <a href={`/admin/profiles/${profile.id}`}>
                                                <span className="absolute inset-x-0 -top-px bottom-0" />
                                                {profile.fullNameWithHonorific}
                                            </a>
                                        </p>
                                        <p className="mt-1 flex text-xs/5 text-gray-500 dark:text-gray-400">
                                            <span className="relative truncate hover:underline">
                                                {profile.leadershipPositionHumanReadable}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div className="flex shrink-0 items-center gap-x-4">
                                    <div className="hidden sm:flex sm:flex-col sm:items-end">
                                        <p className="mt-1 text-xs/5 text-gray-500 dark:text-gray-400">
                                            {profile.id}
                                        </p>
                                    </div>
                                    <ChevronRightIcon aria-hidden="true" className="size-5 flex-none text-gray-400 dark:text-gray-500" />
                                </div>
                            </li>
                        ))}
                    </ul>
                );
            })()}
        </>
    );
}
