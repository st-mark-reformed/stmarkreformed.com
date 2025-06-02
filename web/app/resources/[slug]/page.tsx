// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React from 'react';
import { notFound } from 'next/navigation';
import { Metadata } from 'next';
import Link from 'next/link';
import { ArrowDownTrayIcon, DocumentArrowDownIcon } from '@heroicons/react/24/outline';
import FindResourceItemBySlug from '../repository/FindResourceItemBySlug';
import { createPageTitle } from '../../createPageTitle';
import Layout from '../../layout/Layout';
import Breadcrumbs from '../../Breadcrumbs';
import typography from '../../typography/typography';

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

    const entry = await FindResourceItemBySlug(slug);

    if (!entry) {
        notFound();
    }

    return {
        title: createPageTitle([
            `${entry.title}`,
            'Resources',
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

    const entry = await FindResourceItemBySlug(slug);

    if (entry === null) {
        notFound();
    }

    return (
        <Layout
            hero={{ heroHeading: entry.title }}
        >

            <Breadcrumbs
                breadcrumbs={[{
                    value: 'All Resources',
                    href: '/resources',
                }]}
                currentBreadcrumb={{ value: entry.title }}
            />
            <div className="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
                {(() => {
                    if (!entry.body) {
                        return null;
                    }

                    return (
                        <div
                            className="prose max-w-none mt-10"
                            dangerouslySetInnerHTML={{
                                __html: typography(entry.body),
                            }}
                        />
                    );
                })()}
                {(() => {
                    if (entry.resourceDownloads.length < 1) {
                        return null;
                    }

                    return (
                        <div className="mt-8 text-center">
                            <div className="mb-6">
                                {entry.resourceDownloads.map((download) => (
                                    <Link
                                        key={download.filename}
                                        href={`/uploads/general/resources/${entry.slug}/${download.filename}`}
                                        className="text-black hover:text-crimson inline-block mx-3"
                                        download
                                    >
                                        <span className="block w-20 h-20 mx-auto mb-4">
                                            <DocumentArrowDownIcon />
                                        </span>
                                        <span className="block">
                                            Download &ldquo;<span className="font-semibold">{download.filename}</span>&rdquo; <span className="-mt-1 align-middle inline-block w-4 h-4"><ArrowDownTrayIcon /></span>
                                        </span>
                                    </Link>
                                ))}
                            </div>
                        </div>
                    );
                })()}
            </div>
        </Layout>
    );
}
