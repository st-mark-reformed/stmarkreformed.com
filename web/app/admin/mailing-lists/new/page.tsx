import React from 'react';
import { Metadata } from 'next';
import { createPageTitle } from '../../../createPageTitle';
import AdminLayout from '../../Layout/AdminLayout';
import HasEditMailingListsRoleGuard from '../HasEditMailingListsRoleGuard/HasEditMailingListsRoleGuard';
import CreateNewMailingListPage from './CreateNewMailingListPage';

export const metadata: Metadata = {
    title: createPageTitle([
        'Create New',
        'Mailing Lists',
        'Admin',
    ]),
};

export default async function Page () {
    return (
        <AdminLayout activeNav="mailingLists">
            <HasEditMailingListsRoleGuard>
                <CreateNewMailingListPage />
            </HasEditMailingListsRoleGuard>
        </AdminLayout>
    );
}
