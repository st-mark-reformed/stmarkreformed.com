import React from 'react';
import { notFound } from 'next/navigation';
import type { Metadata } from 'next';
import GetEditPastorsPage from './GetEditPastorsPage';
import { createPageTitle } from '../../../../createPageTitle';
import AdminLayout from '../../../Layout/AdminLayout';
import HasEditPastorsPageRoleGuard from '../../HasEditPastorsPageRoleGuard/HasEditPastorsPageRoleGuard';
import EditPastorsPagePage from './EditPastorsPagePage';

type Props = {
    params: Promise<{ pastorsPageId: string }>;
};

export async function generateMetadata ({ params }: Props): Promise<Metadata> {
    const { pastorsPageId } = await params;

    const pastorsPageItem = await GetEditPastorsPage(pastorsPageId);

    if (!pastorsPageItem) {
        notFound();
    }

    return {
        title: createPageTitle([
            `Edit Pastor's Page: ${pastorsPageItem?.title}`,
            "Pastor's Page",
            'Admin',
        ]),
    };
}

export default async function Page ({ params }: Props) {
    const { pastorsPageId } = await params;

    return (
        <AdminLayout activeNav="pastorsPage">
            <HasEditPastorsPageRoleGuard>
                <EditPastorsPagePage pastorsPageId={pastorsPageId} />
            </HasEditPastorsPageRoleGuard>
        </AdminLayout>
    );
}
