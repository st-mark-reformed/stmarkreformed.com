import React from 'react';
import { notFound } from 'next/navigation';
import type { Metadata } from 'next';
import GetEditHymnOfTheMonth from './GetEditHymnOfTheMonth';
import { createPageTitle } from '../../../../createPageTitle';
import AdminLayout from '../../../Layout/AdminLayout';
import HasEditHymnsOfTheMonthRoleGuard from '../../HasEditHymnsOfTheMonthRoleGuard/HasEditHymnsOfTheMonthRoleGuard';
import EditHymnOfTheMonthPage from './EditHymnOfTheMonthPage';

type Props = {
    params: Promise<{ hymnOfTheMonthId: string }>;
};

export async function generateMetadata ({ params }: Props): Promise<Metadata> {
    const { hymnOfTheMonthId } = await params;

    const hymnOfTheMonthItem = await GetEditHymnOfTheMonth(hymnOfTheMonthId);

    if (!hymnOfTheMonthItem) {
        notFound();
    }

    return {
        title: createPageTitle([
            `Edit Hymn of the Month: ${hymnOfTheMonthItem?.title}`,
            'Hymns of the Month',
            'Admin',
        ]),
    };
}

export default async function Page ({ params }: Props) {
    const { hymnOfTheMonthId } = await params;

    return (
        <AdminLayout activeNav="hymnsOfTheMonth">
            <HasEditHymnsOfTheMonthRoleGuard>
                <EditHymnOfTheMonthPage hymnOfTheMonthId={hymnOfTheMonthId} />
            </HasEditHymnsOfTheMonthRoleGuard>
        </AdminLayout>
    );
}
