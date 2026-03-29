import React from 'react';
import { ChevronRightIcon } from '@heroicons/react/20/solid';
import PageTitle from '../PageTitle';
import Breadcrumbs from '../Breadcrumbs';
import GetMessages from './GetMessages';

export default async function MessagesPage () {
    const messages = await GetMessages();

    return (
        <>
            <Breadcrumbs />
            <PageTitle
                buttons={[
                    {
                        type: 'secondary',
                        content: 'View All Series',
                        glyph: 'eye',
                        href: '/admin/messages/series',
                    },
                    {
                        type: 'secondary',
                        content: 'New Series',
                        glyph: 'plus',
                        href: '/admin/messages/series/new',
                    },
                    {
                        type: 'primary',
                        content: 'New Message',
                        glyph: 'plus',
                        href: '/admin/messages/new',
                    },
                ]}
            >
                Messages
            </PageTitle>
            {(() => {
                if (messages.length === 0) {
                    return (
                        <div className="text-center">
                            <p className="text-sm/6 text-gray-500 dark:text-gray-400">
                                No messages found.
                            </p>
                        </div>
                    );
                }

                return (
                    <ul className="divide-y divide-gray-100 overflow-hidden bg-white shadow-xs outline-1 outline-gray-900/5 sm:rounded-xl dark:divide-white/5 dark:bg-gray-800/50 dark:shadow-none dark:outline-white/10 dark:sm:-outline-offset-1">
                        {messages.map((message) => (
                            <li
                                key={message.id}
                                className="relative flex justify-between gap-x-6 px-4 py-5 hover:bg-gray-50 sm:px-6 dark:hover:bg-white/2.5"
                            >
                                <div className="flex min-w-0 gap-x-4">
                                    <div className="min-w-0 flex-auto">
                                        <p className="text-sm/6 font-semibold text-gray-900 dark:text-white">
                                            <a href={`/admin/messages/edit/${message.id}`}>
                                                <span className="absolute inset-x-0 -top-px bottom-0" />
                                                {message.title}
                                                <span className="text-xs font-light ml-2">(slug: {message.slug})</span>
                                            </a>
                                        </p>
                                        <p className="mt-1 flex text-xs/5 text-gray-500 dark:text-gray-400">
                                            <span className="truncate">
                                                {message.speaker.fullNameWithHonorific}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div className="flex shrink-0 items-center gap-x-4">
                                    <div className="hidden sm:flex sm:flex-col sm:items-end">
                                        <p className="mt-1 text-sm/5 text-gray-800 dark:text-gray-400">
                                            {message.date}
                                        </p>
                                        <p className="mt-1 text-xs/5 text-gray-500 dark:text-gray-400">
                                            {(() => {
                                                if (!message.passage) {
                                                    return null;
                                                }

                                                return <><span className="font-bold">Text:</span> {message.passage}</>;
                                            })()}
                                            {(() => {
                                                if (!message.series.title) {
                                                    return null;
                                                }

                                                return <><span className="font-bold ml-2">Series:</span> {message.series.title}</>;
                                            })()}
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
