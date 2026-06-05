import React from 'react';
import { notFound } from 'next/navigation';
import type { Metadata } from 'next';
import GetEditMailingList from './GetEditMailingList';
import { createPageTitle } from '../../../../createPageTitle';
import AdminLayout from '../../../Layout/AdminLayout';
import HasEditMailingListsRoleGuard from '../../HasEditMailingListsRoleGuard/HasEditMailingListsRoleGuard';
import EditMailingListPage from './EditMailingListPage';

type Props = {
    params: Promise<{ mailingListId: string }>;
};

export async function generateMetadata ({ params }: Props): Promise<Metadata> {
    const { mailingListId } = await params;

    const mailingList = await GetEditMailingList(mailingListId);

    if (!mailingList) {
        notFound();
    }

    return {
        title: createPageTitle([
            `Edit Mailing List: ${mailingList?.listName}`,
            'Mailing Lists',
            'Admin',
        ]),
    };
}

export default async function Page ({ params }: Props) {
    const { mailingListId } = await params;

    return (
        <AdminLayout activeNav="mailingLists">
            <HasEditMailingListsRoleGuard>
                <EditMailingListPage mailingListId={mailingListId} />
            </HasEditMailingListsRoleGuard>
        </AdminLayout>
    );
}
