import React from 'react';
import { notFound } from 'next/navigation';
import type { Metadata } from 'next';
import GetEditNews from './GetEditNews';
import { createPageTitle } from '../../../../createPageTitle';
import AdminLayout from '../../../Layout/AdminLayout';
import HasEditNewsRoleGuard from '../../HasEditNewsRoleGuard/HasEditNewsRoleGuard';
import EditNewsPage from './EditNewsPage';

type Props = {
    params: Promise<{ newsId: string }>;
};

export async function generateMetadata ({ params }: Props): Promise<Metadata> {
    const { newsId } = await params;

    const newsItem = await GetEditNews(newsId);

    if (!newsItem) {
        notFound();
    }

    return {
        title: createPageTitle([
            `Edit News: ${newsItem?.title}`,
            'News',
            'Admin',
        ]),
    };
}

export default async function Page ({ params }: Props) {
    const { newsId } = await params;

    return (
        <AdminLayout activeNav="news">
            <HasEditNewsRoleGuard>
                <EditNewsPage newsId={newsId} />
            </HasEditNewsRoleGuard>
        </AdminLayout>
    );
}
