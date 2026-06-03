import React from 'react';
import { Metadata } from 'next';
import { createPageTitle } from '../../../createPageTitle';
import AdminLayout from '../../Layout/AdminLayout';
import HasEditNewsRoleGuard from '../HasEditNewsRoleGuard/HasEditNewsRoleGuard';
import CreateNewNewsPage from './CreateNewNewsPage';

export const metadata: Metadata = {
    title: createPageTitle([
        'Create New',
        'News',
        'Admin',
    ]),
};

export default async function Page () {
    return (
        <AdminLayout activeNav="news">
            <HasEditNewsRoleGuard>
                <CreateNewNewsPage />
            </HasEditNewsRoleGuard>
        </AdminLayout>
    );
}
