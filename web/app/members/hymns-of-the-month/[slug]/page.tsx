// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React from 'react';
import { notFound } from 'next/navigation';
import { Metadata } from 'next';
import Link from 'next/link';
import MemberLayout from '../../MemberLayout';
import GetPageData from './GetPageData';
import { createPageTitle } from '../../../createPageTitle';
import Breadcrumbs from '../../../Breadcrumbs';
import typography from '../../../typography/typography';

export const dynamic = 'force-dynamic';

export async function generateMetadata (
    {
        params,
    }: {
        params: Promise<{
            slug: string;
        }>;
    },
): Promise<Metadata> {
    const paramsResolved = await params;

    const pageData = await GetPageData(paramsResolved.slug);

    if (pageData === null) {
        notFound();
    }

    return {
        title: createPageTitle([
            pageData.entry.title,
            'Hymns of the Month',
            'Members Area',
        ]),
    };
}

export default async function Page (
    {
        params,
    }: {
        params: Promise<{
            slug: string;
        }>;
    },
) {
    const paramsResolved = await params;

    const pageData = await GetPageData(paramsResolved.slug);

    if (pageData === null) {
        notFound();
    }

    return (
        <MemberLayout
            heroHeading={`Hymn of the Month: ${pageData.entry.title}`}
            activeNavHref="/members/hymns-of-the-month"
            topOfBodyContent={(
                <Breadcrumbs
                    breadcrumbs={[{
                        value: 'Hymns of the Month',
                        href: '/members/hymns-of-the-month',
                    }]}
                    currentBreadcrumb={{ value: pageData.entry.title }}
                />
            )}
        >
            <div className="max-w-2xl">
                <h2
                    className="text-base font-semibold tracking-wide uppercase bg-clip-text text-transparent bg-gradient-to-r from-teal-700 to-teal-400"
                    dangerouslySetInnerHTML={{
                        __html: typography(`Hymn of the Month for ${pageData.entry.title}`),
                    }}
                />
                <p
                    className="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl"
                    dangerouslySetInnerHTML={{
                        __html: typography(pageData.entry.hymnPsalmName),
                    }}
                />
                <p className="text-lg text-gray-500">
                    The following tools and resources are provided to you as members, attendees, or friends of St. Mark to help you learn and sing the hymns and psalms we learn every month at our Monthly hymn sing. We hope they are a blessing to you.
                </p>
                {(() => {
                    if (!pageData.entry.musicSheetFilePath) {
                        return null;
                    }

                    return (
                        <div className="text-lg text-gray-500">
                            <div className="lg:grid lg:grid-cols-2 lg:gap-8 lg:items-start">
                                <div className="relative z-10">
                                    <div className="prose prose-indigo text-gray-500 mx-auto lg:max-w-none">
                                        <h3>Music sheet download:</h3>
                                    </div>
                                    <div className="mt-3 flex text-base max-w-prose mx-auto lg:max-w-none">
                                        <div className="rounded-md shadow">
                                            <Link
                                                href={`/members/hymns-of-the-month/download/${pageData.entry.musicSheetFilePath}`}
                                                className="w-full flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-crimson hover:bg-crimson-dark not-prose"
                                                download
                                            >
                                                Download Music Sheet
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    );
                })()}
                {(() => {
                    if (pageData.entry.practiceTracks.length < 1) {
                        return null;
                    }

                    return (
                        <div className="lg:max-w-3xl mt-6">
                            <div className="relative z-10">
                                <div className="prose prose-indigo text-gray-500 mx-auto lg:max-w-none">
                                    <h3>Practice Tracks:</h3>
                                    <p>The following practice tracks of the full song mix and the various parts will aid you in learning and strengthening your singing of this piece.</p>
                                </div>
                                {pageData.entry.practiceTracks.map((track) => (
                                    <div
                                        key={track.path}
                                        className="mt-3 flex text-base max-w-prose mx-auto lg:max-w-none"
                                    >
                                        <div className="rounded-md shadow">
                                            <Link
                                                href={`/members/hymns-of-the-month/download/${track.path}`}
                                                className="w-full flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-crimson hover:bg-crimson-dark not-prose"
                                                download
                                            >
                                                {track.title}
                                            </Link>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    );
                })()}
            </div>
        </MemberLayout>
    );
}
