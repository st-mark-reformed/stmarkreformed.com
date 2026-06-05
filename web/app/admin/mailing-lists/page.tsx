import React from 'react';
import { Metadata } from 'next';
import AdminLayout from '../Layout/AdminLayout';
import MailingListsPage from './MailingListsPage';
import HasEditMailingListsRoleGuard from './HasEditMailingListsRoleGuard/HasEditMailingListsRoleGuard';
import { createPageTitle } from '../../createPageTitle';

export const metadata: Metadata = {
    title: createPageTitle([
        'Mailing Lists',
        'Admin',
    ]),
};

export default async function Page (
    {
        searchParams,
    }: {
        searchParams: Promise<{
            keyword?: string;
        }>;
    },
) {
    const { keyword } = await searchParams;

    return (
        <AdminLayout activeNav="mailingLists">
            <HasEditMailingListsRoleGuard>
                <MailingListsPage pageNum={1} keyword={keyword ?? ''} />
            </HasEditMailingListsRoleGuard>
        </AdminLayout>
    );
}
