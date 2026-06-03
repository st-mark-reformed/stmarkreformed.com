import React from 'react';
import { Metadata } from 'next';
import { createPageTitle } from '../../../createPageTitle';
import AdminLayout from '../../Layout/AdminLayout';
import HasEditPastorsPageRoleGuard from '../HasEditPastorsPageRoleGuard/HasEditPastorsPageRoleGuard';
import CreateNewPastorsPagePage from './CreateNewPastorsPagePage';

export const metadata: Metadata = {
    title: createPageTitle([
        'Create New',
        "Pastor's Page",
        'Admin',
    ]),
};

export default async function Page () {
    return (
        <AdminLayout activeNav="pastorsPage">
            <HasEditPastorsPageRoleGuard>
                <CreateNewPastorsPagePage />
            </HasEditPastorsPageRoleGuard>
        </AdminLayout>
    );
}
