// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React from 'react';
import { Metadata } from 'next';
import Link from 'next/link';
import { ChevronRightIcon } from '@heroicons/react/16/solid';
import { createPageTitle } from '../../createPageTitle';
import GetPageData from './GetPageData';
import MemberLayout from '../MemberLayout';
import typography from '../../typography/typography';

export const dynamic = 'force-dynamic';

export const metadata: Metadata = {
    title: createPageTitle([
        'Hymns of the Month',
        'Members Area',
    ]),
};

export default async function Page () {
    const pageData = await GetPageData();

    return (
        <MemberLayout
            heroHeading="Hymns of the Month"
            activeNavHref="/members/hymns-of-the-month"
        >
            <div className="shadow max-w-3xl mx-auto xl:ml-20 2xl:ml-24">
                <ul className="mt-2 divide-y divide-gray-200 overflow-hidden shadow not-prose">
                    {pageData.entries.map((hymnItem) => (
                        <li key={hymnItem.slug} className="not-prose">
                            <Link
                                href={`/members/hymns-of-the-month/${hymnItem.slug}`}
                                className="block px-4 py-4 bg-white hover:bg-gray-50 not-prose"
                            >
                                <span className="flex items-center space-x-4">
                                    <span className="flex-1 flex space-x-2">
                                        <span className="flex flex-col text-gray-500 text-sm ">
                                            <span className=" font-bold">
                                                {hymnItem.title}
                                            </span>
                                            <div dangerouslySetInnerHTML={{
                                                __html: typography(hymnItem.content),
                                            }}
                                            />
                                        </span>
                                    </span>
                                    <ChevronRightIcon className="flex-shrink-0 h-5 w-5 text-gray-400" />
                                </span>
                            </Link>
                        </li>
                    ))}
                </ul>
            </div>
        </MemberLayout>
    );
}
