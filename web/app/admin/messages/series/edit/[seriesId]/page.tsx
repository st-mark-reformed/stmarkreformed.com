import React from 'react';
import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { createPageTitle } from '../../../../../createPageTitle';
import GetEditSeries from './GetEditSeries';
import AdminLayout from '../../../../Layout/AdminLayout';
import HasEditMessagesRoleGuard from '../../../HasEditMessagesRoleGuard/HasEditMessagesRoleGuard';
import EditSeriesPage from './EditSeriesPage';

type Props = {
    params: Promise<{ seriesId: string }>;
};

export async function generateMetadata ({ params }: Props): Promise<Metadata> {
    const { seriesId } = await params;

    const series = await GetEditSeries(seriesId);

    if (!series) {
        notFound();
    }

    return {
        title: createPageTitle([
            `Edit Series: ${series.title}`,
            'Series',
            'Messages',
            'Admin',
        ]),
    };
}

export default async function Page ({ params }: Props) {
    const { seriesId } = await params;

    return (
        <AdminLayout activeNav="messages">
            <HasEditMessagesRoleGuard>
                <EditSeriesPage seriesId={seriesId} />
            </HasEditMessagesRoleGuard>
        </AdminLayout>
    );
}
