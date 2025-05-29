// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React from 'react';
import { notFound } from 'next/navigation';
import { Metadata } from 'next';
import Layout from '../../../layout/Layout';
import FindMenOfTheMarkBySlug from '../repository/FindMenOfTheMarkBySlug';
import { createPageTitle } from '../../../createPageTitle';
import MenOfTheMarkMetaData from '../MenOfTheMarkMetaData';
import typography from '../../../typography/typography';
import Breadcrumbs from '../../../Breadcrumbs';

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

    const entry = await FindMenOfTheMarkBySlug(slug);

    if (!entry) {
        notFound();
    }

    return {
        title: createPageTitle([
            `${entry.title}`,
            MenOfTheMarkMetaData.title,
        ]),
        alternates: {
            types: {
                'application/rss+xml': '/publications/men-of-the-mark-test/rss',
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

    const entry = await FindMenOfTheMarkBySlug(slug);

    if (!entry) {
        notFound();
    }

    return (
        <Layout hero={{ heroHeading: entry.title }}>
            <Breadcrumbs
                breadcrumbs={[{
                    value: 'Men of the Mark',
                    href: '/publications/men-of-the-mark-test',
                }]}
                currentBreadcrumb={{ value: entry.title }}
            />
            <div className="relative">
                <div
                    className="relative mx-auto px-4 pt-4 pb-12 sm:max-w-5xl sm:px-14 sm:pt-6 sm:pb-14 md:pt-10 md:pb-24"
                >
                    <div
                        className="mt-3 text-lg text-gray-600 prose max-w-none"
                        dangerouslySetInnerHTML={{
                            __html: typography(entry.bodyHtml),
                        }}
                    />
                </div>
            </div>
        </Layout>
    );
}
