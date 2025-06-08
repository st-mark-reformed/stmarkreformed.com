// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React from 'react';
import { ChevronDoubleRightIcon } from '@heroicons/react/16/solid';
import FindAllMessagesByPage from '../media/messages/repository/FindAllMessagesByPage';
import EntryDisplay from '../media/messages/EntryDisplay';

export default async function LatestSermon () {
    const sermonsPageData = await FindAllMessagesByPage(1);

    const entries = sermonsPageData?.entries;

    if (!entries || !entries[0]) {
        return null;
    }

    const entry = entries[0];

    console.log(entry);

    return (
        <div className="bg-saddle-brown relative overflow-hidden">
            <div
                className="transform scale-110 bg-cover bg-no-repeat bg-center filter blur-xs opacity-40 absolute inset-0 z-0"
                style={{
                    backgroundImage: "url('/images/sermons/featured-sermon.jpg')",
                }}
            />
            <div
                className="max-w-3xl lg:max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center relative z-10 text-center lg:text-left"
            >
                <div className="lg:w-1/2 lg:flex-1">
                    <h2 className="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                        Latest Sermon
                    </h2>
                    <p className="mt-3 max-w-3xl text-lg leading-6 text-gray-300">
                        {entry.by?.title}, {entry.postDateDisplay}
                    </p>
                    <a
                        href="/media/messages"
                        className="mt-6 shadow-lg inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50"
                    >
                        See all sermons <ChevronDoubleRightIcon className="ml-2 mt-0.5 w-5 h-5 text-gray-600" />
                    </a>
                </div>
                <div className="mt-8 lg:w-1/2 lg:mt-0 lg:ml-8">
                    <div className="bg-gray-50 shadow-lg rounded-lg w-full">
                        <EntryDisplay
                            baseUri="/media/messages"
                            entry={entry}
                            showPermalink
                        />
                    </div>
                </div>
            </div>
        </div>
    );
}
