// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/self-closing-comp,jsx-a11y/iframe-has-title */
import React from 'react';
import { Metadata } from 'next';
import { notFound } from 'next/navigation';
import Link from 'next/link';
import Image from 'next/image';
import Layout from '../../../layout/Layout';
import FindGalleryEntryBySlug from '../repository/FindGalleryEntryBySlug';
import { createPageTitle } from '../../../createPageTitle';
import FindAllGalleryEntries from '../repository/FindAllGalleryEntries';
import Breadcrumbs from '../../../Breadcrumbs';

export const dynamic = 'force-static';

export async function generateStaticParams (): Promise<Array<{
    slug: string;
}>> {
    const allGalleries = FindAllGalleryEntries();

    return allGalleries.map((entry) => ({ slug: entry.slug }));
}

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

    const entry = FindGalleryEntryBySlug(paramsResolved.slug);

    if (!entry) {
        notFound();
    }

    return {
        title: createPageTitle([
            `${entry.title}`,
            'Photo Galleries',
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

    const entry = FindGalleryEntryBySlug(paramsResolved.slug);

    if (!entry) {
        notFound();
    }

    return (
        <Layout hero={{ heroHeading: `Photo Gallery: ${entry.title}` }}>
            <div className="min-h-screen-minus-header-and-footer">
                <div className="min-h-screen-minus-header-and-footer overflow-hidden md:flex">
                    <div className="flex-1 flex flex-col">
                        <Breadcrumbs
                            breadcrumbs={[{
                                value: 'All Galleries',
                                href: '/media/galleries-test',
                            }]}
                            currentBreadcrumb={{ value: entry.title }}
                        />
                        {(() => {
                            if (entry.videos.length < 1) {
                                return null;
                            }

                            return (
                                <div className="pt-4">
                                    <h2 className="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl px-4">Videos</h2>
                                    <div className="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3 3xl:grid-cols-4 gap-4 p-4">
                                        {entry.videos.map((youTubeId) => (
                                            <div
                                                key={youTubeId}
                                                className="aspect-w-16 aspect-h-9"
                                            >
                                                <iframe
                                                    width="1280"
                                                    height="720"
                                                    src={`https://www.youtube.com/embed/${youTubeId}?feature=oembed&rel=0`}
                                                    frameBorder="0"
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                    referrerPolicy="strict-origin-when-cross-origin"
                                                    allowFullScreen
                                                />
                                            </div>
                                        ))}
                                    </div>

                                    <h2 className="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl px-4">Pictures</h2>
                                </div>
                            );
                        })()}
                        <div className="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3 3xl:grid-cols-4 gap-4 p-4">
                            {entry.pictures.map((picture) => (
                                <Link
                                    key={picture}
                                    href={picture}
                                    target="_blank"
                                    className="block relative h-auto max-w-full"
                                >
                                    <div className="relative w-full" style={{ paddingBottom: '75%' }}>
                                        <Image
                                            className="object-contain"
                                            src={picture}
                                            sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw"
                                            alt=""
                                            fill
                                        />
                                    </div>
                                </Link>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
