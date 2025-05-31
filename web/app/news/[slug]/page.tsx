// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React from 'react';
import { notFound } from 'next/navigation';
import { Metadata } from 'next';
import Layout from '../../layout/Layout';
import FindNewsItemBySlug from '../repository/FindNewsItemBySlug';
import Breadcrumbs from '../../Breadcrumbs';
import typography from '../../typography/typography';
import { createPageTitle } from '../../createPageTitle';
import MenOfTheMarkMetaData from '../../publications/men-of-the-mark/MenOfTheMarkMetaData';

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
    const { slug } = await params;

    const entry = await FindNewsItemBySlug(slug);

    if (!entry) {
        notFound();
    }

    return {
        title: createPageTitle([
            `${entry.title}`,
            'News',
        ]),
        alternates: {
            types: {
                'application/rss+xml': '/news/rss',
            },
        },
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
    const { slug } = await params;

    const entry = await FindNewsItemBySlug(slug);

    if (entry === null) {
        notFound();
    }

    return (
        <Layout
            hero={{
                heroHeading: entry.title,
                heroParagraph: entry.readableDate,
            }}
        >
            <Breadcrumbs
                breadcrumbs={[{
                    value: 'All News',
                    href: '/news',
                }]}
                currentBreadcrumb={{ value: entry.title }}
            />
            <div className="relative bg-white overflow-hidden">
                <div
                    className="text-lg text-gray-600 prose max-w-none -mt-4 -mb-4 sm:-mt-8 sm:-mb-8 md:-mt-12 md:-mb-12"
                    dangerouslySetInnerHTML={{
                        __html: typography(entry.content),
                    }}
                />
            </div>
        </Layout>
    );
}
