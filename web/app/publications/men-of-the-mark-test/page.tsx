import React from 'react';
import { Metadata } from 'next';
import Link from 'next/link';
import FindAllMenOfTheMarkEntries from './repository/FindAllMenOfTheMarkEntries';
import { createPageTitle } from '../../createPageTitle';
import Layout from '../../layout/Layout';

export const dynamic = 'force-dynamic';

export const metadata: Metadata = {
    title: createPageTitle('Men of the Mark Publications'),
};

export default async function Page () {
    const { entries } = await FindAllMenOfTheMarkEntries();

    console.log(entries);

    return (
        <Layout
            hero={{
                heroHeading: 'Men of the Mark Publications',
                heroParagraph: 'And then all the host of Rohan burst into song, and they sang as they slew, for the joy of battle was on them, and the sound of their singing that was fair and terrible came even to the City.',
            }}
        >
            <div className="relative">
                <div className="relative mx-auto px-4 pt-4 pb-12 sm:max-w-5xl sm:px-14 sm:pt-6 sm:pb-14 md:pt-10 md:pb-24">
                    <div className="mt-3 text-lg text-gray-600 prose max-w-none">
                        <p>Men of the Mark is an occasional newsletter ministry of St. Mark by Pastor Joe Thacker, providing resources to encourage and develop biblical, godly men.</p>
                        <p>Below are the publications thus far. You may also wish to <Link className="font-bold" href="/publications/men-of-the-mark/rss">subscribe via RSS</Link> in your favorite News/RSS reader.</p>
                        <ul>
                            {entries.map((entry) => {
                                console.log('here');

                                return (
                                    <li key={entry.uid}>
                                        <p>
                                            <Link
                                                className="font-bold"
                                                href={`/publications/men-of-the-mark-test/${entry.slug}`}
                                            >
                                                {entry.title}
                                            </Link>
                                        </p>
                                    </li>
                                );
                            })}
                            {/* {% for publication in publications.items %}
                            <li><p><a href="{{ publication.url }}">{{publication.title}}</a></p></li>
                            {% endfor %} */}
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
