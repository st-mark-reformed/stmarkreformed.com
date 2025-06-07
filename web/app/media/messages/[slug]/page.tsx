import React from 'react';
import { notFound } from 'next/navigation';
import { Metadata } from 'next';
import EntryDisplay from '../EntryDisplay';
import Breadcrumbs from '../../../Breadcrumbs';
import { createPageTitle } from '../../../createPageTitle';
import FindMessageBySlug from '../repository/FindMessageBySlug';
import MessagesLayout from '../MessagesLayout';

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

    const pageData = await FindMessageBySlug(slug);

    if (pageData === null) {
        notFound();
    }

    return {
        title: createPageTitle([
            pageData.entry.title,
            'Messages',
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
    const { slug } = await params;

    const pageData = await FindMessageBySlug(slug);

    if (pageData === null) {
        notFound();
    }

    return (
        <MessagesLayout
            heroHeading="Messages"
            topOfBodyContent={(
                <Breadcrumbs
                    breadcrumbs={[{
                        value: 'Messages',
                        href: '/media/messages',
                    }]}
                    currentBreadcrumb={{ value: pageData.entry.title }}
                />
            )}
        >
            <EntryDisplay
                baseUri="/media/messages"
                entry={pageData.entry}
            />
        </MessagesLayout>
    );
}
