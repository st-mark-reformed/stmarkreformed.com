import React from 'react';
import { Metadata } from 'next';
import AdminLayout from '../Layout/AdminLayout';
import ResourcesPage from './ResourcesPage';
import HasEditResourcesRoleGuard from './HasEditResourcesRoleGuard/HasEditResourcesRoleGuard';
import { createPageTitle } from '../../createPageTitle';

export const metadata: Metadata = {
    title: createPageTitle([
        'Resources',
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
        <AdminLayout activeNav="resources">
            <HasEditResourcesRoleGuard>
                <ResourcesPage pageNum={1} keyword={keyword ?? ''} />
            </HasEditResourcesRoleGuard>
        </AdminLayout>
    );
}
