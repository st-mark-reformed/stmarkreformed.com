import React from 'react';
import { Metadata } from 'next';
import Link from 'next/link';
import FindAllMenOfTheMarkEntries from './repository/FindAllMenOfTheMarkEntries';
import { createPageTitle } from '../../createPageTitle';
import Layout from '../../layout/Layout';
import MenOfTheMarkMetaData from './MenOfTheMarkMetaData';

export const dynamic = 'force-dynamic';

export const metadata: Metadata = {
    title: createPageTitle(MenOfTheMarkMetaData.title),
    alternates: {
        types: {
            'application/rss+xml': '/publications/men-of-the-mark/rss',
        },
    },
};

export default async function Page () {
    const { entries } = await FindAllMenOfTheMarkEntries();

    return (
        <Layout
            hero={{
                heroHeading: MenOfTheMarkMetaData.title,
                heroParagraph: 'And then all the host of Rohan burst into song, and they sang as they slew, for the joy of battle was on them, and the sound of their singing that was fair and terrible came even to the City.',
            }}
        >
            <div className="relative">
                <div className="relative mx-auto px-4 pt-4 pb-12 sm:max-w-5xl sm:px-14 sm:pt-6 sm:pb-14 md:pt-10 md:pb-24">
                    <div className="mt-3 text-lg text-gray-600 prose max-w-none">
                        <p>{MenOfTheMarkMetaData.description}</p>
                        <p>Below are the publications thus far. You may also wish to <Link className="font-bold" href="/publications/men-of-the-mark/rss">subscribe via RSS</Link> in your favorite News/RSS reader.</p>
                        <ul>
                            {entries.map((entry) => (
                                <li key={entry.uid}>
                                    <p>
                                        <Link
                                            className="font-bold"
                                            href={`/publications/men-of-the-mark/${entry.slug}`}
                                        >
                                            {entry.title}
                                        </Link>
                                    </p>
                                </li>
                            ))}
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
