import React, { ReactNode } from 'react';
import { headers } from 'next/headers';
import Layout from '../../layout/Layout';
import SidebarInnerLayout from '../../layout/SidebarInnerLayout';
import SidebarSubscribeLink from './SidebarSubscribeLink';
import FindRecentSeries from './repository/FindRecentSeries';

export default async function MessagesLayout (
    {
        children,
        heroHeading,
        topOfBodyContent = null,
        bottomOfBodyContent = null,
    }: {
        children: ReactNode;
        heroHeading: string;
        topOfBodyContent?: ReactNode | string | null;
        bottomOfBodyContent?: ReactNode | string | null;
    },
) {
    const headersList = await headers();
    const pathname = headersList.get('middleware-pathname');

    const recentSeries = await FindRecentSeries();

    return (
        <Layout hero={{ heroHeading }}>
            <SidebarInnerLayout
                reducedPadding
                topOfBodyContent={topOfBodyContent}
                bottomOfBodyContent={bottomOfBodyContent}
                navSections={[
                    {
                        id: 'subscribe',
                        heading: 'Subscribe',
                        nav: [
                            {
                                href: 'https://podcasts.apple.com/us/podcast/messages-from-st-mark-reformed-church/id1619717042',
                                content: <SidebarSubscribeLink
                                    content="Apple Podcasts"
                                    iconHref="/images/podcast/apple-podcsts.svg"
                                />,
                            },
                            {
                                href: 'https://pca.st/zuihey9g',
                                content: <SidebarSubscribeLink
                                    content="Pocket Casts"
                                    iconHref="/images/podcast/pocketcasts.svg"
                                />,
                            },
                            {
                                href: 'https://overcast.fm/itunes1619717042',
                                content: <SidebarSubscribeLink
                                    content="Overcast"
                                    iconHref="/images/podcast/overcast.svg"
                                />,
                            },
                            {
                                href: 'https://castro.fm/itunes/1619717042',
                                content: <SidebarSubscribeLink
                                    content="Castro"
                                    iconHref="/images/podcast/castro.svg"
                                />,
                            },
                            {
                                href: '/media/messages-test/feed',
                                content: <SidebarSubscribeLink
                                    content="RSS Feed"
                                    iconHref="/images/podcast/rss.svg"
                                />,
                            },
                        ],
                    },
                    {
                        id: 'messages-by',
                        heading: 'Messages by',
                        nav: [
                            {
                                href: '/media/messages-test/by/joe-thakcer',
                                content: 'Rev. Joe Thacker (Pastor)',
                                isActive: pathname?.startsWith('/media/messages-test/by/joe-thakcer'),
                            },
                            {
                                href: '/media/messages-test/by/burke-shade',
                                content: 'Rev. Burke Shade (Associate Pastor)',
                                isActive: pathname?.startsWith('/media/messages-test/by/burke-shade'),
                            },
                        ],
                    },
                    {
                        id: 'most-recent-series',
                        heading: 'Most recent series',
                        nav: recentSeries.map((series) => ({
                            href: `/media/messages-test/series/${series.slug}`,
                            content: series.title,
                            isActive: pathname?.startsWith(`/media/messages-test/series/${series.slug}`),
                        })),
                    },
                ]}
            >
                {children}
            </SidebarInnerLayout>
        </Layout>
    );
}
