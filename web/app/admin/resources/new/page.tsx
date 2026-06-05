import React from 'react';
import { Metadata } from 'next';
import { createPageTitle } from '../../../createPageTitle';
import AdminLayout from '../../Layout/AdminLayout';
import HasEditResourcesRoleGuard from '../HasEditResourcesRoleGuard/HasEditResourcesRoleGuard';
import CreateNewResourcePage from './CreateNewResourcePage';

export const metadata: Metadata = {
    title: createPageTitle([
        'Create New',
        'Resources',
        'Admin',
    ]),
};

export default async function Page () {
    return (
        <AdminLayout activeNav="resources">
            <HasEditResourcesRoleGuard>
                <CreateNewResourcePage />
            </HasEditResourcesRoleGuard>
        </AdminLayout>
    );
}
